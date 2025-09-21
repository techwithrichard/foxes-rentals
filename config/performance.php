<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Optimization Settings
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for performance optimization
    | features in the Foxes Rentals application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching settings for optimal performance.
    |
    */
    'cache' => [
        'default_ttl' => env('CACHE_DEFAULT_TTL', 3600), // 1 hour
        'stats_ttl' => env('CACHE_STATS_TTL', 3600), // 1 hour
        'list_ttl' => env('CACHE_LIST_TTL', 1800), // 30 minutes
        'dashboard_ttl' => env('CACHE_DASHBOARD_TTL', 900), // 15 minutes
        'health_ttl' => env('CACHE_HEALTH_TTL', 300), // 5 minutes
        
        'warmup_enabled' => env('CACHE_WARMUP_ENABLED', true),
        'warmup_schedule' => env('CACHE_WARMUP_SCHEDULE', '0 */6 * * *'), // Every 6 hours
        
        'redis_prefix' => env('CACHE_REDIS_PREFIX', 'foxes_rentals:'),
        'redis_serializer' => env('CACHE_REDIS_SERIALIZER', 'php'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    |
    | Configure database optimization settings.
    |
    */
    'database' => [
        'query_logging' => env('DB_QUERY_LOGGING', false),
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        
        'connection_pooling' => env('DB_CONNECTION_POOLING', true),
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
        
        'index_optimization' => env('DB_INDEX_OPTIMIZATION', true),
        'table_optimization' => env('DB_TABLE_OPTIMIZATION', true),
        
        'query_cache' => env('DB_QUERY_CACHE', true),
        'query_cache_size' => env('DB_QUERY_CACHE_SIZE', '64M'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure queue settings for background job processing.
    |
    */
    'queue' => [
        'default_connection' => env('QUEUE_CONNECTION', 'redis'),
        'max_attempts' => env('QUEUE_MAX_ATTEMPTS', 3),
        'timeout' => env('QUEUE_TIMEOUT', 300), // 5 minutes
        
        'batch_size' => env('QUEUE_BATCH_SIZE', 100),
        'retry_after' => env('QUEUE_RETRY_AFTER', 90),
        
        'monitor_enabled' => env('QUEUE_MONITOR_ENABLED', true),
        'monitor_threshold' => env('QUEUE_MONITOR_THRESHOLD', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    |
    | Configure asset optimization settings.
    |
    */
    'assets' => [
        'minification' => env('ASSET_MINIFICATION', true),
        'compression' => env('ASSET_COMPRESSION', true),
        'versioning' => env('ASSET_VERSIONING', true),
        
        'css_minification' => env('ASSET_CSS_MINIFICATION', true),
        'js_minification' => env('ASSET_JS_MINIFICATION', true),
        
        'image_optimization' => env('ASSET_IMAGE_OPTIMIZATION', true),
        'image_quality' => env('ASSET_IMAGE_QUALITY', 85),
        
        'cdn_enabled' => env('ASSET_CDN_ENABLED', false),
        'cdn_url' => env('ASSET_CDN_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configure performance monitoring settings.
    |
    */
    'monitoring' => [
        'enabled' => env('PERFORMANCE_MONITORING', true),
        'sample_rate' => env('PERFORMANCE_SAMPLE_RATE', 0.1), // 10%
        
        'thresholds' => [
            'database_connection_time' => env('PERF_DB_CONNECTION_THRESHOLD', 100), // ms
            'database_query_time' => env('PERF_DB_QUERY_THRESHOLD', 50), // ms
            'cache_write_time' => env('PERF_CACHE_WRITE_THRESHOLD', 10), // ms
            'cache_read_time' => env('PERF_CACHE_READ_THRESHOLD', 5), // ms
            'memory_usage_percentage' => env('PERF_MEMORY_THRESHOLD', 80), // %
        ],
        
        'alerting' => [
            'enabled' => env('PERF_ALERTING_ENABLED', true),
            'email' => env('PERF_ALERT_EMAIL'),
            'slack_webhook' => env('PERF_SLACK_WEBHOOK'),
        ],
        
        'reporting' => [
            'enabled' => env('PERF_REPORTING_ENABLED', true),
            'schedule' => env('PERF_REPORT_SCHEDULE', '0 0 * * *'), // Daily
            'retention_days' => env('PERF_REPORT_RETENTION', 30),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Performance
    |--------------------------------------------------------------------------
    |
    | Configure API performance settings.
    |
    */
    'api' => [
        'rate_limiting' => [
            'enabled' => env('API_RATE_LIMITING', true),
            'default_limit' => env('API_RATE_LIMIT', 60), // requests per minute
            'burst_limit' => env('API_BURST_LIMIT', 100),
        ],
        
        'response_caching' => [
            'enabled' => env('API_RESPONSE_CACHING', true),
            'ttl' => env('API_CACHE_TTL', 300), // 5 minutes
            'vary_headers' => ['Authorization', 'Accept'],
        ],
        
        'pagination' => [
            'default_per_page' => env('API_DEFAULT_PER_PAGE', 15),
            'max_per_page' => env('API_MAX_PER_PAGE', 100),
        ],
        
        'compression' => [
            'enabled' => env('API_COMPRESSION', true),
            'level' => env('API_COMPRESSION_LEVEL', 6),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Background Jobs
    |--------------------------------------------------------------------------
    |
    | Configure background job settings.
    |
    */
    'jobs' => [
        'payment_processing' => [
            'enabled' => env('JOB_PAYMENT_PROCESSING', true),
            'timeout' => env('JOB_PAYMENT_TIMEOUT', 120),
            'retry_attempts' => env('JOB_PAYMENT_RETRIES', 3),
        ],
        
        'report_generation' => [
            'enabled' => env('JOB_REPORT_GENERATION', true),
            'timeout' => env('JOB_REPORT_TIMEOUT', 300),
            'retry_attempts' => env('JOB_REPORT_RETRIES', 2),
        ],
        
        'cache_warmup' => [
            'enabled' => env('JOB_CACHE_WARMUP', true),
            'timeout' => env('JOB_CACHE_TIMEOUT', 300),
            'retry_attempts' => env('JOB_CACHE_RETRIES', 2),
        ],
        
        'email_notifications' => [
            'enabled' => env('JOB_EMAIL_NOTIFICATIONS', true),
            'timeout' => env('JOB_EMAIL_TIMEOUT', 60),
            'retry_attempts' => env('JOB_EMAIL_RETRIES', 3),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Performance
    |--------------------------------------------------------------------------
    |
    | Configure security-related performance settings.
    |
    */
    'security' => [
        'password_hashing' => [
            'algorithm' => env('PASSWORD_HASH_ALGORITHM', 'bcrypt'),
            'rounds' => env('PASSWORD_HASH_ROUNDS', 12),
        ],
        
        'session_optimization' => [
            'driver' => env('SESSION_DRIVER', 'redis'),
            'lifetime' => env('SESSION_LIFETIME', 120), // minutes
            'encrypt' => env('SESSION_ENCRYPT', true),
        ],
        
        'csrf_protection' => [
            'enabled' => env('CSRF_PROTECTION', true),
            'token_regeneration' => env('CSRF_TOKEN_REGENERATION', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Performance
    |--------------------------------------------------------------------------
    |
    | Configure logging performance settings.
    |
    */
    'logging' => [
        'level' => env('LOG_LEVEL', 'info'),
        'driver' => env('LOG_DRIVER', 'stack'),
        
        'performance_logging' => env('PERFORMANCE_LOGGING', true),
        'query_logging' => env('QUERY_LOGGING', false),
        
        'log_rotation' => [
            'enabled' => env('LOG_ROTATION', true),
            'max_files' => env('LOG_MAX_FILES', 5),
            'max_size' => env('LOG_MAX_SIZE', '10M'),
        ],
    ],
];
