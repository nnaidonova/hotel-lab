<?php
declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => (int)(getenv('DB_PORT') ?: '3306'),
        'name' => getenv('DB_NAME') ?: 'hotel_complex',
        'user' => getenv('DB_USER') ?: 'db_admin',
        'pass' => getenv('DB_PASS') ?: 'DbAdmin_StrongPass1!',
        'charset' => 'utf8mb4',
    ],
];
