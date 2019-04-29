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

  public function __construct($version = '1.0', $encoding = 'utf-8', $standalone = false, $formatOutput = true)
  {
    $this->xml = new \DomDocument($version, $encoding);
    $this->xml->xmlStandalone = $standalone;
    $this->xml->formatOutput = $formatOutput;
  }

  /**
   * Convert array to XML
   * 
   * @param array $arr
   */
  public function convertToXML($arr = []): Array2XML
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
    $xml = $this->xml;

    $node = $xml->createElement($nodeName);

    if (is_array($arr))
    {
      $this->_addAttributes($node, $arr);
      $this->_addTextNode($node, $arr);
      $this->_addTreeNode($node, $arr);
    }

    if (!is_null($arr) && !is_array($arr))
    {
      $node->appendChild($xml->createTextNode($this->valuesToString($arr)));
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
//      var_dump($key, $this->isValidTagName($key));
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
  public function getXML() : string
  {
    return $this->xml->saveXML();
  }

}

