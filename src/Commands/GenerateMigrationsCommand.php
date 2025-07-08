<?php

namespace Vendor\DbIntrospector\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Vendor\DbIntrospector\DatabaseInspector;

class GenerateMigrationsCommand extends Command
{
    protected $signature = 'db:generate-migrations
                            {--tables=* : Specific tables to generate migrations for}
                            {--rewrite : Overwrite existing migration files if they exist}
                            {--output-dir= : Place generated migrations into this folder under database/migrations}';
    protected $description = 'Generate Laravel migration files from the existing database schema';

    public function handle(DatabaseInspector $inspector)
    {
        $tables   = $this->option('tables') ?: $inspector->getTables();
        $rewrite  = $this->option('rewrite');
        $dirOption = $this->option('output-dir') ?: '';

        $basePath = database_path('migrations');
        $outputPath = $dirOption
            ? rtrim($basePath . '/' . $dirOption, '/')
            : $basePath;

        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
            $this->info("Created output directory: {$outputPath}");
        }

        foreach ($tables as $table) {
            $columns       = $inspector->getTableColumns($table);
            $migrationName = 'create_' . $table . '_table';
            $timestamp     = date('Y_m_d_His');
            $filename      = "{$timestamp}_{$migrationName}.php";
            $filePath      = $outputPath . '/' . $filename;

            // Skip if exists and not rewriting
            if (file_exists($filePath) && ! $rewrite) {
                $this->warn("Skipping existing migration: {$filename}");
                continue;
            }

            $stub      = file_get_contents(__DIR__ . '/../Templates/migration.stub');
            $className = Str::studly($migrationName);

            $fields    = $this->buildFields($columns);

            $content   = str_replace(
                ['{{class}}', '{{table}}', '{{fields}}'],
                [$className, $table, $fields],
                $stub
            );

            file_put_contents($filePath, $content);
            $action = file_exists($filePath) && $rewrite ? 'Overwritten' : 'Created';
            $this->info("{$action} migration: {$filename}");

            // Prevent collisions for same-second migrations
            sleep(1);
        }

        $this->info('All migrations processed successfully!');
    }

    protected function buildFields(array $columns): string
    {
        $lines = [];

        foreach ($columns as $column) {
            $name     = $column->getName();
            $type     = $column->getType()->getName();
            $method   = match ($type) {
                'integer'  => 'integer',
                'smallint' => 'smallInteger',
                'bigint'   => 'bigInteger',
                'string'   => 'string',
                'text'     => 'text',
                'datetime' => 'dateTime',
                'date'     => 'date',
                'time'     => 'time',
                'boolean'  => 'boolean',
                default    => $type,
            };
            $nullable = $column->getNotnull() ? '' : '->nullable()';

            $lines[] = "\$table->{$method}('{$name}'){$nullable};";
        }

        return implode("\n            ", $lines);
    }
}
