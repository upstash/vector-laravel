<?php

namespace Upstash\Vector\Laravel\Console\Commands\Concerns;

use Upstash\Vector\Contracts\IndexInterface;
use Upstash\Vector\Laravel\VectorManager;

trait ConnectionOptionTrait
{
    protected function getConnection(): IndexInterface
    {
        $connection = $this->option('connection');
        if (! $connection) {
            $connection = 'default';
        }

        return app(VectorManager::class)->connection($connection);
    }
}
