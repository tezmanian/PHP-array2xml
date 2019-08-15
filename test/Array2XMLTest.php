<?php

/**
 * Array2XML (https://github.com/tezmanian/PHP-array2xml)
 *
 * @copyright Copyright (c) 2018-2019 René Halberstadt
 * @license   https://opensource.org/licenses/Apache-2.0
 */

namespace TezTest\Array2XML;

use PHPUnit\Framework\TestCase;
use Tez\Array2XML\Array2XML;
use Tez\Array2XML\Exception\InvalidAttributeException;
use Tez\Array2XML\Exception\InvalidTagException;

final class Array2XMLTest extends TestCase
{

    public function testIfIsRightXML()
    {

        $two_movies = [
            'movies' => [
                'movie' => [
                    [
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

        $xmlToCheck = <<< MOVIES
<?xml version="1.0" encoding="utf-8" standalone="no"?>
<movies>
  <movie>
    <title>Pulp Fiction</title>
    <director URI="https://en.wikipedia.org/wiki/Quentin_Tarantino">Quentin Tarantino</director>
    <release_date>1994</release_date>
    <budget>8.5 million USD</budget>
    <actors>
      <actor>
        <name>Vincent Vega</name>
        <cast>John Travolta</cast>
      </actor>
      <actor>
        <name>Jules Winnfield</name>
        <cast>Samuel L. Jackson</cast>
      </actor>
      <actor>
        <name>Mia Wallace</name>
        <cast>Uma Thurman</cast>
      </actor>
      <actor>
        <name>Butch Coolidge</name>
        <cast>Bruce Willis</cast>
      </actor>
      <actor>
        <name>Winston Wolf</name>
        <cast>Harvey Keitel</cast>
      </actor>
    </actors>
  </movie>
  <movie>
    <title>Jackie Brown</title>
    <director URI="https://en.wikipedia.org/wiki/Quentin_Tarantino">Quentin Tarantino</director>
    <release_date>1997</release_date>
    <actors>
      <actor>
        <name>Jacqueline „Jackie“ Brown</name>
        <cast>Pam Grier</cast>
      </actor>
      <actor>
        <name>Ordell Robbie</name>
        <cast>Samuel L. Jackson</cast>
      </actor>
    </actors>
  </movie>
</movies>

MOVIES;

        try
        {
            $xml = Array2XML::convertToXML($two_movies);
            $this->assertSame($xml->getXML(), $xmlToCheck);
        } catch (InvalidAttributeException $e)
        {
            $this->assertFalse(true,"InvaildAttributeException");
        } catch (InvalidTagException $e)
        {
            $this->assertFalse(true,"InvaildTagException");
        }

    }

    /**
     * @expectedException \Tez\Array2XML\Exception\InvalidTagException
     * @throws InvalidAttributeException
     */
    public function testIfInvalidTagException()
    {
        $arr = [
            'movies' => [
                'movie' => [
                    [
                        'title' => 'Jackie Brown',
                        'director' => [
                            '@value' => 'Quentin Tarantino',
                            '@attributes' =>
                                [
                                    'URI' => 'https://en.wikipedia.org/wiki/Quentin_Tarantino'
                                ],
                        ],
                        'release date' => '1997',
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
                    ]
                ]
            ]
        ];
        Array2XML::convertToXML($arr);
    }

}
