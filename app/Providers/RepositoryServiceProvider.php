<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class RepositoryServiceProvider extends ServiceProvider
{
    public $serviceBindings = [
        'App\Repositories\Interfaces\UserRepositoryInterface' => 'App\Repositories\UserRepository',
        'App\Repositories\Interfaces\ProvinceRepositoryInterface' => 'App\Repositories\ProvinceRepository',
        'App\Repositories\Interfaces\DistrictRepositoryInterface' => 'App\Repositories\DistrictRepository',
        'App\Repositories\Interfaces\RouterRepositoryInterface' => 'App\Repositories\RouterRepository',
        'App\Repositories\Interfaces\UserCatalogueRepositoryInterface' => 'App\Repositories\UserCatalogueRepository',
        'App\Repositories\Interfaces\LanguageRepositoryInterface' => 'App\Repositories\LanguageRepository',
        'App\Repositories\Interfaces\PostCatalogueRepositoryInterface' => 'App\Repositories\PostCatalogueRepository',
        'App\Repositories\Interfaces\PostRepositoryInterface' => 'App\Repositories\PostRepository',
        'App\Repositories\Interfaces\PermissionRepositoryInterface' => 'App\Repositories\PermissionRepository',
        'App\Repositories\Interfaces\GenerateRepositoryInterface' => 'App\Repositories\GenerateRepository',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach($this->serviceBindings as $interface => $service){
            $this->app->bind($interface, $service);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
