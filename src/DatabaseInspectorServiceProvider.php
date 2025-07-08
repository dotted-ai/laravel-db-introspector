<?php

namespace Vendor\DbIntrospector;

use Illuminate\Support\ServiceProvider;
use Vendor\DbIntrospector\Commands\GenerateMigrationsCommand;

class DatabaseInspectorServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the inspector
        $this->app->singleton(DatabaseInspector::class, function ($app) {
            return new DatabaseInspector($app['db']->connection());
        });
    }

    public function boot()
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateMigrationsCommand::class,
            ]);
        }

        // Publish stub
        $this->publishes([
            __DIR__ . '/Templates/migration.stub' => database_path('stubs/db-introspector/migration.stub'),
        ], 'stubs');
    }
}
