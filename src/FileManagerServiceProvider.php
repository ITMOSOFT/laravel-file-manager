<?php

namespace ItmoSoft\FileManager;

use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/file-manager.php', 'itmosoft-file-manager');
        $this->app->singleton(FileManager::class);
        $this->app->alias(FileManager::class, 'itmosoft.file-manager');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/file-manager.php' => config_path('itmosoft-file-manager.php'),
        ], 'itmosoft-file-manager-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
