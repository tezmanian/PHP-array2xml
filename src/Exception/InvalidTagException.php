<?php

/**
 * Halberstadt Array2XML (https://repo.root-zone.info/PHP/halberstadt-array2xml)
 *
 * @copyright Copyright (c) 2018-2019 René Halberstadt
 * @license   https://opensource.org/licenses/Apache-2.0
 */

namespace Halberstadt\Array2XML\Exception;

/**
 * Description of InvalidTagException
 *
 * @author halberstadt
 */
class InvalidTagException extends \Exception
{
  public function __construct(string $message = "", int $code = 0, \Throwable $previous = NULL): \Exception
  {
    parent::__construct($message, $code, $previous);
  }
}
