<?php

namespace graphicmarket\kirby2fa;

return [
    '2fa' => [
        'computed' => [
            'userHas2faAuthentication' => function () {
                return kirby()->user()->has2FA();
            },
        ],
    ],
];