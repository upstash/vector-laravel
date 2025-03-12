<?php

namespace Upstash\Vector\Laravel\Commands\Concerns;

use Upstash\Vector\Laravel\Exceptions\MissingConnectionException;
use Upstash\Vector\Laravel\Exceptions\MissingCredentialsException;

trait HandlesGeneralExceptionsTrait
{
    public function decorateHandler(callable $callback): int
    {
        try {
            return $callback();
        } catch (MissingCredentialsException $e) {
            $message = sprintf(
                'Credentials for %s connection are missing, please make sure you have added them to your .env file.',
                $e->connectionName,
            );
            $this->outputComponents()->error($message);

            return self::FAILURE;
        } catch (MissingConnectionException $e) {
            $message = sprintf(
                'Connection %s does not exist on your vector.php config file, please make sure you add it.',
                $e->connectionName,
            );
            $this->outputComponents()->error($message);

            return self::FAILURE;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
