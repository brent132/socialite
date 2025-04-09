<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Query Performance Settings
    |--------------------------------------------------------------------------
    |
    | Here you can configure various options to optimize database performance.
    |
    */
    'database' => [
        // Maximum number of records to select at once to avoid memory issues
        'chunk_size' => env('DB_CHUNK_SIZE', 1000),
        
        // Enable query cache
        'query_cache_enabled' => env('DB_QUERY_CACHE_ENABLED', true),
        
        // Default TTL for query cache in seconds
        'query_cache_ttl' => env('DB_QUERY_CACHE_TTL', 60),
        
        // Whether to log slow queries
        'log_slow_queries' => env('DB_LOG_SLOW_QUERIES', true),
        
        // Threshold in milliseconds for slow query logging
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Memory Optimization
    |--------------------------------------------------------------------------
    |
    | Configure memory limits and optimizations
    |
    */
    'memory' => [
        // Enable strict memory limits
        'strict_limits' => env('MEMORY_STRICT_LIMITS', false),
        
        // Maximum memory limit for background jobs (in MB)
        'job_memory_limit' => env('JOB_MEMORY_LIMIT', 512),
        
        // Enable garbage collection optimization
        'optimize_gc' => env('OPTIMIZE_GARBAGE_COLLECTION', true),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure global cache settings
    |
    */
    'cache' => [
        // Default cache driver
        'default_driver' => env('CACHE_DRIVER', 'file'),
        
        // Whether to use tags (not supported in file or database drivers)
        'use_tags' => env('CACHE_USE_TAGS', false),
        
        // Lock timeout for cache operations in seconds
        'lock_timeout' => env('CACHE_LOCK_TIMEOUT', 5),
        
        // Enable result caching for static content
        'static_cache_enabled' => env('STATIC_CACHE_ENABLED', true),
        
        // Static content cache TTL in seconds
        'static_cache_ttl' => env('STATIC_CACHE_TTL', 3600), // 1 hour
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Request Throttling
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for API requests
    |
    */
    'throttling' => [
        // Enable API rate limiting
        'enabled' => env('API_RATE_LIMITING_ENABLED', true),
        
        // Maximum requests per minute for authenticated users
        'authenticated_rate' => env('API_AUTH_RATE_LIMIT', 60),
        
        // Maximum requests per minute for guests
        'guest_rate' => env('API_GUEST_RATE_LIMIT', 30),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Job Queue Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for optimizing queue workers
    |
    */
    'queue' => [
        // Default queue connection
        'default_connection' => env('QUEUE_CONNECTION', 'sync'),
        
        // Maximum time a job can run in seconds
        'job_timeout' => env('QUEUE_JOB_TIMEOUT', 60),
        
        // Number of times to attempt a failed job
        'job_attempts' => env('QUEUE_JOB_ATTEMPTS', 3),
        
        // Whether to retry failed jobs
        'retry_failed' => env('QUEUE_RETRY_FAILED', true),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    |
    | Configure options for optimizing frontend assets
    |
    */
    'assets' => [
        // Whether to minify CSS and JS
        'minify' => env('ASSETS_MINIFY', true),
        
        // Whether to use versioning for assets
        'versioning' => env('ASSETS_VERSIONING', true),
        
        // Whether to combine CSS and JS files
        'combine' => env('ASSETS_COMBINE', true),
        
        // Whether to use gzip compression
        'gzip' => env('ASSETS_GZIP', true),
    ],
]; 