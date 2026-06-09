<?php

return [

    'url' => env('SUPABASE_URL', 'https://mmbtxvjynuvkvoohwgud.supabase.co'),

    's3' => [
        'key' => env('SUPABASE_S3_ACCESS_KEY_ID'),
        'secret' => env('SUPABASE_S3_SECRET_ACCESS_KEY'),
        'region' => env('SUPABASE_S3_REGION', 'us-west-2'),
        'endpoint' => env('SUPABASE_S3_ENDPOINT', 'https://mmbtxvjynuvkvoohwgud.storage.supabase.co/storage/v1/s3'),
        'use_path_style_endpoint' => env('SUPABASE_S3_USE_PATH_STYLE_ENDPOINT', true),
    ],

    'buckets' => [
        'compras' => env('SUPABASE_BUCKET_COMPRAS', 'compras-dev'),
        'vendas' => env('SUPABASE_BUCKET_VENDAS', 'vendas-dev'),
    ],

    'disks' => [
        'compras' => 'supabase_compras',
        'vendas' => 'supabase_vendas',
    ],

];
