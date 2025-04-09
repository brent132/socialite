<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge performance-related configuration
        $this->mergeConfigFrom(
            base_path('config/performance.php'), 'performance'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only apply optimizations in production
        if (app()->environment('production')) {
            // Disable wrapping of JSON resources
            JsonResource::withoutWrapping();
            
            // Enable model strict mode to catch N+1 queries
            Model::shouldBeStrict(!app()->isProduction());
            
            // Prevent lazy loading in production to avoid N+1 query issues
            Model::preventLazyLoading();
            
            // Set default string length for schema
            Schema::defaultStringLength(191);
            
            // Enable query log in non-production environments only
            if (!app()->isProduction()) {
                DB::enableQueryLog();
            } else {
                DB::disableQueryLog();
            }
            
            // Only catch when a specific exception is related to lazy loading
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);
                logger()->warning("Attempted to lazy load [{$relation}] on model [{$class}]");
            });
        }
        
        // Apply global query cache macros
        $this->registerQueryBuilderMacros();
    }
    
    /**
     * Register custom query builder macros for caching
     */
    protected function registerQueryBuilderMacros(): void
    {
        // Add custom method to cache query results
        \Illuminate\Database\Query\Builder::macro('remember', function ($ttl = 60, $key = null) {
            $builder = $this;
            $key = $key ?: 'query_' . md5(serialize([
                $builder->toSql(),
                $builder->getBindings(),
                get_class($builder)
            ]));
            
            return Cache::remember($key, now()->addSeconds($ttl), function () use ($builder) {
                return $builder->get();
            });
        });
    }
}
