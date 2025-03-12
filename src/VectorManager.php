<?php

namespace Upstash\Vector\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Upstash\Vector\Contracts\IndexInterface;
use Upstash\Vector\Contracts\IndexNamespaceInterface;
use Upstash\Vector\DataQuery;
use Upstash\Vector\DataQueryResult;
use Upstash\Vector\DataUpsert;
use Upstash\Vector\Index;
use Upstash\Vector\IndexInfo;
use Upstash\Vector\Iterators\VectorRangeIterator;
use Upstash\Vector\Laravel\Exceptions\MissingConnectionException;
use Upstash\Vector\Laravel\Exceptions\MissingCredentialsException;
use Upstash\Vector\NamespaceInfo;
use Upstash\Vector\VectorDeleteByMetadataFilter;
use Upstash\Vector\VectorDeleteByPrefix;
use Upstash\Vector\VectorDeleteResult;
use Upstash\Vector\VectorFetch;
use Upstash\Vector\VectorFetchByPrefix;
use Upstash\Vector\VectorFetchResult;
use Upstash\Vector\VectorMatch;
use Upstash\Vector\VectorQuery;
use Upstash\Vector\VectorQueryManyResult;
use Upstash\Vector\VectorQueryResult;
use Upstash\Vector\VectorRange;
use Upstash\Vector\VectorRangeResult;
use Upstash\Vector\VectorUpdate;
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

    public function delete(array|string|VectorDeleteByPrefix|VectorDeleteByMetadataFilter $ids): VectorDeleteResult
    {
        return $this->getDefaultConnection()->delete($ids);
    }

    public function fetch(VectorFetch|VectorFetchByPrefix $vectorFetch): VectorFetchResult
    {
        return $this->getDefaultConnection()->fetch($vectorFetch);
    }

    private function loadIndexFromConfig(string $connectionName): IndexInterface
    {
        $connection = Arr::get($this->getConfig(), sprintf('connections.%s', $connectionName));
        if ($connection === null) {
            throw new MissingConnectionException(
                message: sprintf('Connection "%s" not found in config', $connectionName),
                connectionName: $connectionName,
            );
        }

        ['url' => $url, 'token' => $token] = $connection;

        if ($url === null && $token === null) {
            throw new MissingCredentialsException(
                message: 'url and token are missing',
                connectionName: $connectionName,
            );
        }

        if ($url === null) {
            throw new MissingCredentialsException('url is missing', connectionName: $connectionName);
        }

        if ($token === null) {
            throw new MissingCredentialsException('token is missing', connectionName: $connectionName);
        }

        return new Index($url, $token);
    }

    private function getConfig(): array
    {
        return $this->app['config']['vector'];
    }

    public function queryMany(array $queries): VectorQueryManyResult
    {
        return $this->getDefaultConnection()->queryMany($queries);
    }

    public function random(): ?VectorMatch
    {
        return $this->getDefaultConnection()->random();
    }

    public function update(VectorUpdate $update): void
    {
        $this->getDefaultConnection()->update($update);
    }

    public function listNamespaces(): array
    {
        return $this->getDefaultConnection()->listNamespaces();
    }

    public function range(VectorRange $range): VectorRangeResult
    {
        return $this->getDefaultConnection()->range($range);
    }

    public function rangeIterator(VectorRange $range): VectorRangeIterator
    {
        return $this->getDefaultConnection()->rangeIterator($range);
    }
}
