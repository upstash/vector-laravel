<?php

namespace Upstash\Vector\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Upstash\Vector\Contracts\IndexInterface;
use Upstash\Vector\Contracts\IndexNamespaceInterface;
use Upstash\Vector\DataQuery;
use Upstash\Vector\DataQueryResult;
use Upstash\Vector\DataUpsert;
use Upstash\Vector\IndexInfo;
use Upstash\Vector\NamespaceInfo;
use Upstash\Vector\VectorDeleteResult;
use Upstash\Vector\VectorFetch;
use Upstash\Vector\VectorFetchResult;
use Upstash\Vector\VectorQuery;
use Upstash\Vector\VectorQueryResult;
use Upstash\Vector\VectorUpsert;

/**
 * @method static IndexNamespaceInterface namespace(string $namespace)
 * @method static IndexInfo getInfo()
 * @method static void resetAll()
 * @method static NamespaceInfo getNamespaceInfo()
 * @method static void reset()
 * @method static void deleteNamespace()
 * @method static void upsert(VectorUpsert $vector)
 * @method static void upsertMany(array $vectors)
 * @method static void upsertData(DataUpsert $data)
 * @method static void upsertDataMany(array $data)
 * @method static VectorQueryResult query(VectorQuery $query)
 * @method static DataQueryResult queryData(DataQuery $query)
 * @method static VectorDeleteResult delete(array $ids)
 * @method static VectorFetchResult fetch(VectorFetch $vectorFetch)
 * @method static IndexInterface connection(string $connection)
 *
 * @see \Upstash\Vector\Laravel\VectorManager
 * @see \Upstash\Vector\Index
 */
class Vector extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'vector.manager';
    }
}
