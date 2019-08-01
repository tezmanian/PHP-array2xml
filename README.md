# halberstadt-array2xml

A little method I needed to convert an array easy to yml.

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

$xml = \Halberstadt\Array2XML\Array2XML::convertToXML();
$xml->getXML()

```
