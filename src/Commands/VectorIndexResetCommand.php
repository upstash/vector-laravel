<?php

namespace Upstash\Vector\Laravel\Commands;

use Illuminate\Console\Command;
use Upstash\Vector\Laravel\Commands\Concerns\ConnectionOptionTrait;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;

class VectorIndexResetCommand extends Command
{
    use ConnectionOptionTrait;

    public $signature = 'vector:index:reset {--C|connection=default}';

    public $description = 'Resets all the namespaces on an index';

    public function handle(): int
    {
        $index = $this->getConnection();

        if (! confirm(
            label: 'Are you sure you want to reset all the namespaces?',
            hint: 'This action cannot be undone.',
        )) {
            info('All good, nothing was done.');

            return self::SUCCESS;
        }

        $this->components->task(
            'Resetting all the namespaces',
            fn () => $index->resetAll(),
        );

        $this->components->success('All the namespaces were reset.');

        return self::SUCCESS;
    }
}
