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

class VectorNamespaceDeleteCommand extends Command
{
    use ConnectionOptionTrait;
    use HandlesGeneralExceptionsTrait;

    public $signature = 'vector:namespace:delete
        {namespaces?* : List of namespace names to delete}
        {--C|connection=default : Connection name to be used}
    ';

    public $description = 'Deletes a specific namespace from the index';

    public function handle(): int
    {
        return $this->decorateHandler(
            $this->handleSafely(...)
        );
    }

    public function handleSafely(): int
    {
        $index = $this->getConnection();
        $namespacesToDelete = $this->getNamespacesToDelete($index)->unique();

        if ($namespacesToDelete->isEmpty()) {
            $this->components->info('This index does not have any namespaces');

            return self::SUCCESS;
        }

        $namespaceInfos = $this->getNamespacesInformation($index, $namespacesToDelete->toArray());
        $vectorsThatWillBeDeleted = $namespaceInfos->sum(fn (NamespaceInfo $namespaceInfo) => $namespaceInfo->vectorCount);
        $namespacesThatWillBeDeleted = $namespaceInfos->count();

        if ($namespacesThatWillBeDeleted === 0) {
            $this->components->error('None of the namespaces you selected exist');

            return self::FAILURE;
        }

        $namespacesThatExist = $namespaceInfos->keys();
        $namespacesThatCannotBeDeleted = $namespacesToDelete->diff($namespacesThatExist);
        $namespacesToDelete = $namespacesToDelete->intersect($namespacesThatExist);

        if ($namespacesThatCannotBeDeleted->isNotEmpty()) {
            $namespacesThatCannotBeDeleted->each(
                fn (string $namespace) => $this->components->error(sprintf('Namespace %s does not exist', $namespace))
            );
        }

        if (! confirm(
            label: $namespacesToDelete->count() > 1
                ? 'Are you sure you want to delete those namespaces'
                : sprintf('Are you sure you want to delete namespace %s?', $namespacesToDelete->first()),
            hint: sprintf(
                '%s vectors will be deleted across %s namespaces. (This action cannot be undone)',
                $vectorsThatWillBeDeleted,
                $namespacesThatWillBeDeleted,
            ),
        )) {
            $this->components->info('Nothing was deleted.');

            return self::SUCCESS;
        }

        foreach ($namespacesToDelete as $namespace) {
            if ($namespace === '') {
                $this->components->error('You cannot delete the "default" namespace');

                if ($namespacesToDelete->count() === 1) {
                    return self::FAILURE;
                }

                continue;
            }

            $this->components->task(
                sprintf('Deleting namespace %s', $namespace),
                fn () => $index->namespace($namespace)->deleteNamespace(),
            );
        }

        $this->components->success('Namespaces were deleted.');

        return self::SUCCESS;
    }

    private function getNamespacesToDelete(IndexInterface $index): Collection
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
