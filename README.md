# array2xml

A little class I created to convert an array easy to yml.

#### Usage

```php

$arr = [
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
      ]
    ]
  ]
]

$xml = \Tez\Array2XML\Array2XML::convertToXML($arr);
$xml->getXML()

```
