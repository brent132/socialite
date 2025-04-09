<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. Depending on your PHP setup, you can choose one of them.
    |
    | Included options:
    |   - \Intervention\Image\Drivers\Gd\Driver::class
    |   - \Intervention\Image\Drivers\Imagick\Driver::class
    |
    */

    'driver' => \Intervention\Image\Drivers\Gd\Driver::class,

    /*
    |--------------------------------------------------------------------------
    | Configuration Options
    |--------------------------------------------------------------------------
    |
    | These options control the behavior of Intervention Image.
    |
    | - "autoOrientation" controls whether an imported image should be
    |    automatically rotated according to any existing Exif data.
    |
    | - "decodeAnimation" decides whether a possibly animated image is
    |    decoded as such or whether the animation is discarded.
    |
    | - "blendingColor" Defines the default blending color.
    |
    | - "strip" controls if meta data like exif tags should be removed when
    |    encoding images.
    */

    'options' => [
        'autoOrientation' => true,
        'decodeAnimation' => true,
        'blendingColor' => 'ffffff',
        'strip' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    |
    | This array of options will be passed to the intervention image
    | library when encoding images. These options can significantly
    | reduce image file sizes and improve loading performance.
    |
    */
    
    'optimization' => [
        'jpg' => [
            'quality' => env('IMAGE_JPG_QUALITY', 80), // 0-100, lower means more compression
        ],
        'jpeg' => [
            'quality' => env('IMAGE_JPEG_QUALITY', 80),
        ],
        'png' => [
            'compression_level' => env('IMAGE_PNG_COMPRESSION', 7), // 0-9, higher means more compression
        ],
        'webp' => [
            'quality' => env('IMAGE_WEBP_QUALITY', 80),
            'lossless' => env('IMAGE_WEBP_LOSSLESS', false),
        ],
        'avif' => [
            'quality' => env('IMAGE_AVIF_QUALITY', 70),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Progressive Loading
    |--------------------------------------------------------------------------
    |
    | Enable progressive loading for JPEGs to improve perceived performance
    |
    */
    'progressive' => env('IMAGE_PROGRESSIVE', true),
    
    /*
    |--------------------------------------------------------------------------
    | Image Caching
    |--------------------------------------------------------------------------
    |
    | Define caching settings for processed images
    |
    */
    'cache' => [
        'enabled' => env('IMAGE_CACHE_ENABLED', true),
        'ttl' => env('IMAGE_CACHE_TTL', 86400), // 24 hours in seconds
        'path' => env('IMAGE_CACHE_PATH', 'storage/image-cache'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Responsive Image Settings
    |--------------------------------------------------------------------------
    |
    | Configure responsive image presets
    |
    */
    'responsive' => [
        'enabled' => env('RESPONSIVE_IMAGES_ENABLED', true),
        'sizes' => [
            'xs' => ['width' => 200, 'height' => null],
            'sm' => ['width' => 400, 'height' => null],
            'md' => ['width' => 800, 'height' => null],
            'lg' => ['width' => 1200, 'height' => null],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Preloading
    |--------------------------------------------------------------------------
    |
    | Configuration for image preloading
    |
    */
    'preload' => [
        'enabled' => env('IMAGE_PRELOAD_ENABLED', true),
        'critical' => ['profile', 'logo'], // Image types to preload
    ],
];
