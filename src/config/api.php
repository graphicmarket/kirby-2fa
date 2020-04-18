<?php

namespace graphicmarket\kirby2fa;

return [
    'routes' => function ($kirby) {
        return [
            [
                'pattern' => 'kirby-2fa/auth/login',
                'method' => 'POST',
                'auth' => false,
                'action' => function () {
                    return Login::auth();
                },
            ],
            [
                'pattern' => 'kirby-2fa/auth/code',
                'method' => 'POST',
                'auth' => false,
                'action' => function () {
                    return Login::auth2FA();
                },
            ],
            [
                'pattern' => 'kirby-2fa/secret',
                'method' => 'POST',
                'action' => function () {

                    $auth = new TwoFactorAuthentication();

                    return [
                        'secret' => $auth->getSecret(),
                        'qr' => $auth->getQRCode(),
                    ];
                },
            ],
            [
                'pattern' => 'kirby-2fa/disable',
                'method' => 'POST',
                'action' => function () use ($kirby) {
                    return [
                        'disabled' => $kirby->user()->disable2FA(),
                    ];
                },
            ],
            [
                'pattern' => 'kirby-2fa/verify',
                'method' => 'POST',
                'action' => function () use ($kirby) {

                    $auth = new TwoFactorAuthentication();
                    $verify = $auth->verifyCode(get('code'));

                    if ($verify) {
                        $kirby->user()->enable2FA($auth->getSecret());
                    }

                    return compact('verify');
                },
            ],
        ];
    },
];