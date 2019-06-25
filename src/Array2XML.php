<?php

/**
 * Halberstadt Array2XML (https://repo.root-zone.info/PHP/halberstadt-array2xml)
 *
 * @copyright Copyright (c) 2018-2019 René Halberstadt
 * @license   https://opensource.org/licenses/Apache-2.0
 */

namespace Halberstadt\Array2XML;

use Halberstadt\Array2XML\Exception\
{
  InvalidAttributeException,
  InvalidTagException
};

/**
 * Description of Array2XML
 *
 * @author halberstadt
 */
class Array2XML
{

  /**
   * @var string
   */
  private $encoding = 'UTF-8';

  /**
   * @var DomDocument|null
   */
  private $xml = null;
  private $_namespace = [];

  private function __construct(string $version = '1.0', string $encoding = 'utf-8', bool $standalone = false, bool $formatOutput = true)
  {
    $this->xml = new \DomDocument($version, $encoding);
    $this->xml->xmlStandalone = $standalone;
    $this->xml->formatOutput = $formatOutput;
  }

  public static function convertToXML(array $arr = [], string $version = '1.0', string $encoding = 'utf-8', bool $standalone = false, bool $formatOutput = true) : Array2XML
  {
    $xml = new self($version, $encoding, $standalone, $formatOutput);
    return $xml->_convertToXML($arr);
  }
  
  /**
   * Convert array to XML
   * 
   * @param array $arr
   */
  private function _convertToXML(array $arr = []): Array2XML
  {
    $xml = $this->xml;

    foreach ($arr as $nodeName => $value)
    {
      $xml->appendChild($this->_convert($nodeName, $value));
    }
    return $this;
  }

  /**
   * Append the node with an element
   * 
   * @param string $nodeNameToAppend
   * @param array $arr
   */
  public function appendElementToNode($nodeNameToAppend, $arr = []): Array2XML
  {
    $element = $this->xml->getElementsByTagName($nodeNameToAppend);

    if (is_array($arr))
    {
      $element->item(0)->parentNode->appendChild($this->_convert($nodeNameToAppend, $arr));
    }
    return $this;
  }

  /**
   * Add an element to a node.
   * 
   * @param string $nodeNameToAppend
   * @param string $elementName
   * @param array $arr
   */
  public function addElementToNode($nodeNameToAdd, $elementName, $arr = []): Array2XML
  {

    $element = $this->xml->getElementsByTagName($nodeNameToAdd);
    
    if (is_array($arr))
    {
      $element->item(0)->appendChild($this->_convert($elementName, $arr));
    }
    return $this;
  }

  /**
   * Add an element to tree with XPath query
   * 
   * @param string $xPath
   * @param string $elementName
   * @param array $arr
   */
  public function addElementToNodeByXpath($xPath, $elementName, $arr = []): Array2XML
  {

    $_xpath = new DOMXPath($this->xml);

    if (is_array($arr))
    {
      $_xpath->query($xPath)->item(0)->appendChild($this->_convert($elementName, $arr));
    }
    return $this;
  }

  /**
   * Convert values to string
   * 
   * @param string|bool $value
   * @return string
   */
  private function valuesToString($value): string
  {
    return (string) ($value === true) ? 'true' : ($value === false) ? 'false' : $value;
  }

  /**
   * Loop method to convert the array
   * 
   * @param string $nodeName
   * @param array|string $arr
   * @return \DOMElement
   */
  private function _convert($nodeName, $arr = []): \DOMElement
  {

    $ns = false;

    if (!is_null($arr) && is_array($arr) && array_key_exists('@namespace', $arr))
    {
      array_unshift($this->_namespace, $arr['@namespace']);
      if (!is_array($this->_namespace[0]) ||
              !array_key_exists('prefix', $this->_namespace[0]) ||
              !array_key_exists('uri', $this->_namespace[0]))
      {
        throw new InvalidAttributeException('Namespace is missing attributes');
      }
      $nodeName = sprintf('%s:%s', $this->_namespace[0]['prefix'], $nodeName);
      $node = $this->xml->createElementNS($arr['@namespace']['uri'], $nodeName);
      $ns = true;
      unset($arr['@namespace']);
    } else
    {
      if (!empty($this->_namespace))
      {
        $nodeName = sprintf('%s:%s', $this->_namespace[0]['prefix'], $nodeName);
      }
      $node = $this->xml->createElement($nodeName);
    }

    if (is_array($arr))
    {
      $this->_addAttributes($node, $arr);
      $this->_addTextNode($node, $arr);
      $this->_addTreeNode($node, $arr);
    }

    if (!is_null($arr) && !is_array($arr))
    {
      $node->appendChild($this->xml->createTextNode($this->valuesToString($arr)));
    }
    if (!empty($this->_namespace) && $ns == true)
    {
      array_shift($this->_namespace);
    }
    return $node;
  }

  /**
   * Add attributes to the element
   * 
   * @param \DOMElement $node
   * @param type $arr
   * @throws Exception
   */
  private function _addAttributes(&$node, &$arr): void
  {
    if (array_key_exists('@attributes', $arr) && is_array($arr['@attributes']))
    {
      foreach ($arr['@attributes'] as $key => $value)
      {
        if (!$this->isValidTagName($key))
        {
          throw new InvalidAttributeException('Illegal character in attribute name.');
        }
        $node->setAttribute($key, $this->valuesToString($value));
      }
      unset($arr['@attributes']);
    }
  }

  /**
   * Add a text node to the element.
   * 
   * @param \DOMElement $node
   * @param array $arr
   * @return boolean
   */
  private function _addTextNode(&$node, &$arr): void
  {
    if (array_key_exists('@value', $arr))
    {
      $node->appendChild($this->xml->createTextNode($this->valuesToString($arr['@value'])));
      $arr = [];
    }
  }

  /**
   * Add an element tree to the node, looping _convert method.
   * 
   * @param \DOMElement $node
   * @param type $arr
   * @throws Exception
   */
  private function _addTreeNode(&$node, &$arr): void
  {
    foreach ($arr as $key => $value)
    {
      if (!$this->isValidTagName($key))
      {
        throw new InvalidTagException('Illegal character in tag name ' . $key);
      }
      if (is_array($value) && is_numeric(key($value)))
      {
        foreach ($value as $val)
        {
          $node->appendChild($this->_convert($key, $val));
        }
      } else
      {
        $node->appendChild($this->_convert($key, $value));
      }
      unset($arr[$key]);
    }
  }

  /**
   * Returns the XML encoding
   * 
   * @return string
   */
  public function getEncoding(): string
  {
    return $this->encoding;
  }

  /**
   * Check if tag name is valid
   * 
   * @param string $tagName
   * @return bool
   */
  private function isValidTagName($tagName): bool
  {
    $pattern = '!^[a-z_]+[a-z0-9\:\-\.\_]*[^: ]*$!i';
    return preg_match($pattern, $tagName, $matches) && $matches[0] == $tagName;
  }

  /**
   * Returns the XML
   * 
   * @return string
   */
  public function getXML(): string
  {
    return $this->xml->saveXML();
  }

}

$two_movies = [
  'movies' => [
    'movie' => [
      [
        '@namespace' => [
          'prefix' => 'move',
          'uri' => 'http://halberstadt.ws/movies',
        ],
        'title' => 'Pulp Fiction',
        'director' => [
          '@value' => 'Quentin Tarantino',
          '@attributes' =>
          [
            'URI' => 'https://en.wikipedia.org/wiki/Quentin_Tarantino'
          ],
        ],
        'release_date' => '1994',
        'budget' => '8.5 million USD',
        'actors' => [
          'actor' => [
            [
              'name' => 'Vincent Vega',
              'cast' => 'John Travolta'
            ],
            [
              'name' => 'Jules Winnfield',
              'cast' => 'Samuel L. Jackson'
            ],
            [
              'name' => 'Mia Wallace',
              'cast' => 'Uma Thurman'
            ],
            [
              'name' => 'Butch Coolidge',
              'cast' => 'Bruce Willis'
            ],
            [
              'name' => 'Winston Wolf',
              'cast' => 'Harvey Keitel'
            ],
          ],
        ]
      ],
      [
        '@namespace' => [
          'prefix' => 'mov',
          'uri' => 'http://halberstadt.ws/movi',
        ],
        'title' => 'Jackie Brown',
        'director' => [
          '@value' => 'Quentin Tarantino',
          '@attributes' =>
          [
            'URI' => 'https://en.wikipedia.org/wiki/Quentin_Tarantino'
          ],
        ],
        'release_date' => '1997',
        'actors' => [
          'actor' => [
            [
              'name' => 'Jacqueline „Jackie“ Brown',
              'cast' => 'Pam Grier'
            ],
            [
              'name' => 'Ordell Robbie',
              'cast' => 'Samuel L. Jackson'
            ],
          ],
        ]
      ],
    ]
  ]
];


$a2x = Array2XML::convertToXML($two_movies);
//var_dump($a2x->getXML());
$a2x->addElementToNode('move:actors', 'move:actor', [
              'name' => 'Winston Wolf',
              'cast' => 'Harvey Keitel'
            ]);

//$a2x = new Array2XML();
//$a2x->convertToXML($two_movies);
//var_Dump($a2x->getXML());
