<?php

/**
 * Array2XML (https://github.com/tezmanian/PHP-array2xml)
 *
 * @copyright Copyright (c) 2018-2019 René Halberstadt
 * @license   https://opensource.org/licenses/Apache-2.0
 */

namespace Tez\Array2XML\Exception;

use Exception;
use Throwable;

/**
 * Exception thrown if the tag is invalid
 *
 * @author halberstadt
 */
class InvalidTagException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
