<?php

namespace Vendor\DbIntrospector;

use Illuminate\Support\ServiceProvider;
use Vendor\DbIntrospector\Commands\GenerateMigrationsCommand;

class DatabaseInspectorServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the inspector and the command
        $this->app->singleton(DatabaseInspector::class, function ($app) {
            return new DatabaseInspector($app['db']->connection());
        });

        $this->app->singleton(GenerateMigrationsCommand::class);
        $this->commands([
            GenerateMigrationsCommand::class,
        ]);
    }

    public function boot()
    {
        // Publish stub
        $this->publishes([
            __DIR__ . '/Templates/migration.stub' => database_path('stubs/db-introspector/migration.stub'),
        ], 'stubs');
    }
}
