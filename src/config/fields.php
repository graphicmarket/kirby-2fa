<?php

namespace graphicmarket\kirby2fa;

return [
    '2fa' => [
        'computed' => [
            'userHasTwoFactorAuth' => function () {
                return kirby()->user()->has2FA();
            },
        ],
    ],
];