<?php

namespace Upstash\Vector\Laravel\Commands;

use Illuminate\Console\Command;
use Upstash\Vector\Laravel\Commands\Concerns\ConnectionOptionTrait;

class VectorNamespaceListCommand extends Command
{
    use ConnectionOptionTrait;

    public $signature = 'vector:namespace:list {--C|connection=default}';

    public $description = 'Display the namespaces on an index';

    public function handle(): int
    {
        $index = $this->getConnection();

        $info = $index->getInfo();

        $namespaceCollection = collect($info->namespaces);
        $namespaces = $namespaceCollection->keys()->map(
            fn (string $namespace) => $namespace === '' ? 'default: ""' : $namespace,
        );

        foreach ($namespaces as $namespace) {
            $this->line($namespace);
        }

        return self::SUCCESS;
    }

    private function heading(string $text): void
    {
        $this->components->twoColumnDetail(sprintf('<fg=green;options=bold>%s</>', $text));
    }
}

// vector:info --connection=default

// vector:reset:all --connection=default

// vector:namespace:delete {namespace=''} --connection=default
// vector:namespace:list --connection=default
// vector:namespace:reset {namespace=''} --connection=default

// vector:browse --connection=default
