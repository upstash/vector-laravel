<?php

namespace Upstash\Vector\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Upstash\Vector\Contracts\IndexInterface;
use Upstash\Vector\Laravel\Console\Commands\Concerns\ConnectionOptionTrait;
use Upstash\Vector\Laravel\Console\Commands\Concerns\HandlesGeneralExceptionsTrait;
use Upstash\Vector\NamespaceInfo;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\spin;

class VectorNamespaceResetCommand extends Command
{
    use ConnectionOptionTrait;
    use HandlesGeneralExceptionsTrait;

    public $signature = 'vector:namespace:reset
        {namespaces?* : List of namespace names to reset}
        {--C|connection=default : Connection name to be used}
    ';

    public $description = 'Resets a specific namespace from the index';

    public function handle(): int
    {
        return $this->decorateHandler(
            $this->handleSafely(...)
        );
    }

    public function handleSafely(): int
    {
        $index = $this->getConnection();
        $namespacesToReset = $this->getNamespacesToReset($index)->unique();

        if ($namespacesToReset->isEmpty()) {
            $this->components->info('This index does not have any namespaces');

            return self::SUCCESS;
        }

        $namespaceInfos = $this->getNamespacesInformation($index, $namespacesToReset->toArray());
        $vectorsThatWillBeDeleted = $namespaceInfos->sum(fn (NamespaceInfo $namespaceInfo) => $namespaceInfo->vectorCount);
        $namespacesThatWillBeReset = $namespaceInfos->count();

        if ($namespacesThatWillBeReset === 0) {
            $this->components->error('None of the namespaces you selected exist');

            return self::FAILURE;
        }

        $namespacesThatExist = $namespaceInfos->keys();
        $namespacesThatCannotBeReset = $namespacesToReset->diff($namespacesThatExist);
        $namespacesToReset = $namespacesToReset->intersect($namespacesThatExist);

        if ($namespacesThatCannotBeReset->isNotEmpty()) {
            $namespacesThatCannotBeReset->each(
                fn (string $namespace) => $this->components->error(sprintf('Namespace %s does not exist', $namespace))
            );
        }

        if (! confirm(
            label: $namespacesToReset->count() > 1
                ? 'Are you sure you want to reset those namespaces'
                : sprintf('Are you sure you want to reset namespace %s?', $namespacesToReset->first()),
            hint: sprintf(
                '%s vectors will be deleted across %s namespaces. (This action cannot be undone)',
                $vectorsThatWillBeDeleted,
                $namespacesThatWillBeReset,
            ),
        )) {
            $this->components->info('Nothing was reset.');

            return self::SUCCESS;
        }

        foreach ($namespacesToReset as $namespace) {
            $this->components->task(
                sprintf('Resetting namespace %s', $namespace),
                fn () => $index->namespace($namespace)->reset(),
            );
        }

        $this->components->success('Namespaces were reset.');

        return self::SUCCESS;
    }

    private function getNamespacesToReset(IndexInterface $index): Collection
    {
        $namespacesArgument = $this->argument('namespaces');

        if (! empty($namespacesArgument)) {
            return collect($namespacesArgument);
        }

        $namespaces = spin(
            callback: fn () => collect($index->listNamespaces())->filter(fn (string $namespace) => $namespace !== ''),
            message: 'Fetching namespaces',
        );

        if ($namespaces->isEmpty()) {
            return collect();
        }

        $namespacesToDelete = multiselect(
            label: 'Select the namespaces to delete',
            options: $namespaces->keyBy(fn (string $namespace) => $namespace)->toArray(),
            required: true,
        );

        return collect($namespacesToDelete);
    }

    /**
     * @return Collection<string, NamespaceInfo>
     */
    private function getNamespacesInformation(IndexInterface $index, array $namespaces): Collection
    {
        return spin(
            callback: fn () => collect($index->getInfo()->namespaces)
                ->filter(fn (NamespaceInfo $namespaceInfo, string $namespace) => in_array($namespace, $namespaces)),
            message: 'Fetching namespace information',
        );
    }
}
