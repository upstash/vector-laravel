<?php

namespace Upstash\Vector\Laravel\Exceptions;

use Throwable;

/**
 * @internal This class is not supposed to be used outside of this package.
 */
class MissingConnectionException extends \Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public string $connectionName = ''
    ) {
        parent::__construct($message, $code, $previous);
    }
}
