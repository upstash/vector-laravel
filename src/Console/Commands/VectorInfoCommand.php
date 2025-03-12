<?php

namespace Upstash\Vector\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Upstash\Vector\Enums\IndexType;
use Upstash\Vector\Laravel\Console\Commands\Concerns\ConnectionOptionTrait;
use Upstash\Vector\Laravel\Console\Commands\Concerns\HandlesGeneralExceptionsTrait;

use function Laravel\Prompts\spin;

class VectorInfoCommand extends Command
{
    use ConnectionOptionTrait;
    use HandlesGeneralExceptionsTrait;

    public $signature = 'vector:info {--C|connection=default : Connection name to be used}';

    public $description = 'Displays information about the vector index';

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

        $this->line('');
        $this->heading('Index');
        $this->components->twoColumnDetail('Type', $info->indexType->value);
        $this->components->twoColumnDetail('Dimensions', (string) $info->dimension);
        $this->components->twoColumnDetail('Namespace Count', (string) count($info->namespaces));
        $this->components->twoColumnDetail('Vector Count', (string) $info->vectorCount);
        $this->components->twoColumnDetail('Pending Vectors', (string) $info->pendingVectorCount);
        $this->line('');

        if ($info->indexType === IndexType::DENSE || $info->indexType === IndexType::HYBRID) {
            $this->heading('Dense Vector');
            $this->components->twoColumnDetail('Similarity Function', $info->denseIndex->similarityFunction);
            $this->components->twoColumnDetail('Embedding Model', $info->denseIndex->embeddingModel != '' ? $info->denseIndex->embeddingModel : 'Custom');
            $this->line('');
        }

        if ($info->indexType === IndexType::SPARSE || $info->indexType === IndexType::HYBRID) {
            $this->heading('Sparse Vector');
            $this->components->twoColumnDetail('Embedding Model', $info->sparseIndex?->embeddingModel);
            $this->line('');
        }

        return self::SUCCESS;
    }

    private function heading(string $text): void
    {
        $this->components->twoColumnDetail(sprintf('<fg=green;options=bold>%s</>', $text));
    }
}
