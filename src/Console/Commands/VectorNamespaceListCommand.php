<?php

namespace Upstash\Vector\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Upstash\Vector\Laravel\Console\Commands\Concerns\ConnectionOptionTrait;
use Upstash\Vector\Laravel\Console\Commands\Concerns\HandlesGeneralExceptionsTrait;
use Upstash\Vector\NamespaceInfo;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class VectorNamespaceListCommand extends Command
{
    use ConnectionOptionTrait;
    use HandlesGeneralExceptionsTrait;

    public $signature = 'vector:namespace:list {--C|connection=default : Connection name to be used}';

    public $description = 'Display the namespaces on an index';

    public function handle(): int
    {
        return $this->decorateHandler(
            $this->handleSafely(...)
        );
    }

    public function handleSafely(): int
    {
        $index = $this->getConnection();

        try {
            $info = spin(
                callback: fn () => $index->getInfo(),
                message: 'Fetching your index info',
            );
        } catch (\Exception) {
            $this->components->error('Could not fetch index info');

            return self::FAILURE;
        }

        $namespaces = collect($info->namespaces)
            ->map(fn (NamespaceInfo $namespaceInfo, string $namespace) => [
                'name' => $namespace,
                'vector_count' => $namespaceInfo->vectorCount,
                'pending_vector_count' => $namespaceInfo->pendingVectorCount,
            ])
            // Exclude default vector
            ->map(fn (array $namespace) => [
                ...$namespace,
                'name' => $namespace['name'] !== '' ? $namespace['name'] : '<fg=gray>(default)</>',
            ]);

        table(
            headers: ['Namespace', 'Vector Count', 'Pending Vector Count'],
            rows: $namespaces->map(fn (array $namespace) => [
                $namespace['name'],
                (string) $namespace['vector_count'],
                (string) $namespace['pending_vector_count'],
            ])->toArray(),
        );

        return self::SUCCESS;
    }
}
