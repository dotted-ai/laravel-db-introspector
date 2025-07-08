<?php

namespace Vendor\DbIntrospector;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\Connection;

class DatabaseInspector
{
    /** @var AbstractSchemaManager */
    protected $schemaManager;

    public function __construct(Connection $connection)
    {
        $this->schemaManager = $connection->getDoctrineSchemaManager();
    }

    /**
     * List all tables in the database
     */
    public function getTables(): array
    {
        return $this->schemaManager->listTableNames();
    }

    /**
     * Get column definitions for a given table
     */
    public function getTableColumns(string $table): array
    {
        return $this->schemaManager->listTableColumns($table);
    }
}
