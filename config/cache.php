<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache store that will be used by the
    | framework. This connection is utilized if another isn't explicitly
    | specified when running a cache operation inside the application.
    |
    */

    'default' => env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the time-to-live (TTL) values for different
    | types of cache entries to optimize performance and freshness.
    |
    */
    
    'ttl' => env('CACHE_TTL', 600),

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, or DynamoDB cache
    | stores there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the time-to-live (TTL) values for different
    | types of cache entries to optimize performance and freshness.
    |
    */
    
    'ttl' => [
        'profile_stats' => env('CACHE_TTL_PROFILE_STATS', 300),
        'user_data' => env('CACHE_TTL_USER_DATA', 1800),
        'posts' => env('CACHE_TTL_POSTS', 600),
        'comments' => env('CACHE_TTL_COMMENTS', 120),
        'images' => env('CACHE_TTL_IMAGES', 86400),
        'search_results' => env('CACHE_TTL_SEARCH', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "apc", "array", "database", "file",
    |         "memcached", "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Pattern
    |--------------------------------------------------------------------------
    |
    | When utilizing multiple cache stores, it's useful to define key patterns
    | for common cache keys to ensure consistency across the application.
    |
    */
    
    'key_patterns' => [
        'user' => 'user.{id}',
        'user_profile' => 'user.{id}.profile',
        'user_posts' => 'user.{id}.posts.{page}',
        'post' => 'post.{id}',
        'post_comments' => 'post.{id}.comments.{page}',
        'timeline' => 'timeline.{user_id}.{page}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Optimization
    |--------------------------------------------------------------------------
    |
    | Configure additional cache optimization parameters like compression
    | and serialization methods.
    |
    */
    
    'optimization' => [
        'compression' => env('CACHE_COMPRESSION', true),
        'serializer' => env('CACHE_SERIALIZER', 'php'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache Locks
    |--------------------------------------------------------------------------
    |
    | Control how long locks should be maintained for expensive operations.
    |
    */
    
    'locks' => [
        'ttl' => env('CACHE_LOCK_TTL', 60),  // Default lock TTL in seconds
        'retry_after' => env('CACHE_LOCK_RETRY', 5), // Retry after seconds
        'retry_count' => env('CACHE_LOCK_RETRY_COUNT', 3), // Number of retries
    ],

];
