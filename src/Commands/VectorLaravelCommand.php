<?php

namespace Upstash\VectorLaravel\Commands;

use Illuminate\Console\Command;

class VectorLaravelCommand extends Command
{
    public $signature = 'vector-laravel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
