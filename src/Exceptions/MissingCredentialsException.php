<?php

namespace Upstash\Vector\Laravel\Exceptions;

use Throwable;

class MissingCredentialsException extends \Exception
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
