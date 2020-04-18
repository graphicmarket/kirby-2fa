<?php

namespace graphicmarket\kirby2fa;

use Kirby\Exception\NotFoundException;
use Kirby\Toolkit\Str;

return [
    'route:before' => function ($route, $path, $method) {

        // Block the default kirby route for login
        if (Str::contains($path, 'api/auth/login')) {
            throw new NotFoundException();
        }

    },
];