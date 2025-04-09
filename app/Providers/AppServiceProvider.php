<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageService;
use App\Services\ProfileService;
use App\Services\FollowService;
use App\Repositories\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(ImageService::class);
        $this->app->singleton(ProfileService::class);
        $this->app->singleton(FollowService::class);
        
        // Register repositories
        $this->app->singleton(UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure image directories and default images exist
        $this->app->make(ImageService::class)->ensureImageDirectoriesExist();
    }
}
