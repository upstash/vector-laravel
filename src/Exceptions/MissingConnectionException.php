<?php

namespace Upstash\Vector\Laravel\Exceptions;

use Throwable;

/**
 * @internal This class is not supposed to be used outside of this package.
 */
class MissingConnectionException extends \Exception
{
    public function __construct(
        int $code = 0,
        ?Throwable $previous = null,
        public string $connectionName = ''
    ) {
        parent::__construct("Connection [{$connectionName}] is missing.", $code, $previous);
    }
}
