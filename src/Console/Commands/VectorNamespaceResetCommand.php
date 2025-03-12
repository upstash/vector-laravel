<?php

namespace Upstash\Vector\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Upstash\Vector\Contracts\IndexInterface;
use Upstash\Vector\Laravel\Console\Commands\Concerns\ConnectionOptionTrait;
use Upstash\Vector\Laravel\Console\Commands\Concerns\HandlesGeneralExceptionsTrait;

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

        if (! confirm(
            label: $namespacesToReset->count() > 1
                ? 'Are you sure you want to reset those namespaces'
                : sprintf('Are you sure you want to reset namespace %s?', $namespacesToReset->first()),
            hint: 'This action cannot be undone.',
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
        );

        return collect($namespacesToDelete);
    }
}
