<?php

namespace Upstash\Vector\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Upstash\Vector\Contracts\IndexInterface;
use Upstash\Vector\Contracts\IndexNamespaceInterface;
use Upstash\Vector\DataQuery;
use Upstash\Vector\DataQueryResult;
use Upstash\Vector\DataUpsert;
use Upstash\Vector\Index;
use Upstash\Vector\IndexInfo;
use Upstash\Vector\NamespaceInfo;
use Upstash\Vector\VectorDeleteResult;
use Upstash\Vector\VectorFetch;
use Upstash\Vector\VectorFetchResult;
use Upstash\Vector\VectorQuery;
use Upstash\Vector\VectorQueryResult;
use Upstash\Vector\VectorUpsert;

class VectorManager implements IndexInterface
{
    private array $indexes = [];

    public function __construct(private Application $app) {}

    public function connection(string $name): IndexInterface
    {
        if (! isset($this->indexes[$name])) {
            $this->indexes[$name] = $this->loadIndexFromConfig($name);
        }

        return $this->indexes[$name];
    }

    private function getDefaultConnection(): IndexInterface
    {
        $defaultConnectionName = Arr::get($this->getConfig(), 'default', 'default');

        return $this->connection($defaultConnectionName);
    }

    public function namespace(string $namespace): IndexNamespaceInterface
    {
        return $this->getDefaultConnection()->namespace($namespace);
    }

    public function getInfo(): IndexInfo
    {
        return $this->getDefaultConnection()->getInfo();
    }

    public function resetAll(): void
    {
        $this->getDefaultConnection()->resetAll();
    }

    public function getNamespaceInfo(): NamespaceInfo
    {
        return $this->getDefaultConnection()->getNamespaceInfo();
    }

    public function reset(): void
    {
        $this->getDefaultConnection()->reset();
    }

    public function deleteNamespace(): void
    {
        $this->getDefaultConnection()->deleteNamespace();
    }

    public function upsert(VectorUpsert $vector): void
    {
        $this->getDefaultConnection()->upsert($vector);
    }

    public function upsertMany(array $vectors): void
    {
        $this->getDefaultConnection()->upsertMany($vectors);
    }

    public function upsertData(DataUpsert $data): void
    {
        $this->getDefaultConnection()->upsertData($data);
    }

    public function upsertDataMany(array $data): void
    {
        $this->getDefaultConnection()->upsertDataMany($data);
    }

    public function query(VectorQuery $query): VectorQueryResult
    {
        return $this->getDefaultConnection()->query($query);
    }

    public function queryData(DataQuery $query): DataQueryResult
    {
        return $this->getDefaultConnection()->queryData($query);
    }

    public function delete(array $ids): VectorDeleteResult
    {
        return $this->getDefaultConnection()->delete($ids);
    }

    public function fetch(VectorFetch $vectorFetch): VectorFetchResult
    {
        return $this->getDefaultConnection()->fetch($vectorFetch);
    }

    private function loadIndexFromConfig(string $name): IndexInterface
    {
        $connection = Arr::get($this->getConfig(), sprintf('connections.%s', $name));
        if ($connection === null) {
            // TODO: Change Exception
            throw new InvalidArgumentException(sprintf('Connection "%s" not found in config', $name));
        }

        ['url' => $url, 'token' => $token] = $connection;

        if ($url === null) {
            throw new \Exception('URL cannot be null');
        }

        if ($token === null) {
            throw new \Exception('Token cannot be null');
        }

        return new Index($url, $token);
    }

    private function getConfig(): array
    {
        return $this->app['config']['vector'];
    }
}
