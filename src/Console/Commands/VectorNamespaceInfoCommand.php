<?php

namespace Upstash\Vector\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Upstash\Vector\Laravel\Console\Commands\Concerns\ConnectionOptionTrait;
use Upstash\Vector\Laravel\Console\Commands\Concerns\HandlesGeneralExceptionsTrait;
use Upstash\Vector\NamespaceInfo;

use function Laravel\Prompts\spin;

class VectorNamespaceInfoCommand extends Command
{
    use ConnectionOptionTrait;
    use HandlesGeneralExceptionsTrait;

    public $signature = 'vector:namespace:info {namespace} {--C|connection=default}';

    public $description = 'Display the namespace info';

    public function handle(): int
    {
        return $this->decorateHandler(
            $this->handleSafely(...)
        );
    }

    public function handleSafely(): int
    {
        $index = $this->getConnection();
        $namespace = $this->argument('namespace');

        try {
            /** @var NamespaceInfo $info */
            $info = spin(
                callback: fn () => $index->namespace($namespace)->getNamespaceInfo(),
                message: 'Fetching your index info',
            );
        } catch (\Exception) {
            $this->components->error('Could not fetch the namespace info');

            return self::FAILURE;
        }

        $this->newLine();
        $this->heading(sprintf('Namespace: %s', $namespace));
        $this->components->twoColumnDetail('Vector Count', (string) $info->vectorCount);
        $this->components->twoColumnDetail('Pending Vectors', (string) $info->pendingVectorCount);
        $this->newLine();

        return self::SUCCESS;
    }

    private function heading(string $text, $right = ''): void
    {
        $this->components->twoColumnDetail(sprintf('<fg=green;options=bold>%s</>', $text), $right);
    }
}
