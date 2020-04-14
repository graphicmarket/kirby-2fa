<?php

namespace graphicmarket\kirby2fa;

return [
    'api' => true,
    'issuer' => 'Kirby-2fa panel',
    'database' => dirname(__DIR__, 2) . '/db/kirby-2fa.sqlite',
    'cache.default' => [
        'active' => true,
    ],
];