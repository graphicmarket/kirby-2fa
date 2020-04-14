<?php

namespace graphicmarket\kirby2fa;

return [
    'routes' => function ($kirby) {
        return [
            [
                'pattern' => 'kirby-2fa/auth',
                'method' => 'POST',
                'auth' => false,
                'action' => function () use ($kirby) {

                    if ($email = get('email') and $password = get('password')) {

                        $user = $kirby->user($email);

                        if ($user && $user->validatePassword($password)) {

                            return [
                                'valid' => true,
                                'tfa' => $user->has2FA(),
                            ];

                        } else {
                            return [
                                'valid' => false,
                                'issue' => 'Invalid email or password',
                            ];
                        }

                    }
                },
            ],
            [
                'pattern' => 'kirby-2fa/secret',
                'method' => 'POST',
                'action' => function () {

                    $auth = new Authenticator();

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

                    $auth = new Authenticator();
                    $verify = $auth->verifyCode(get('code'));

                    if ($verify) {
                        $kirby->user()->enable2FA($auth->getSecret());
                    }

                    return compact('verify');
                },
            ],
            [
                'pattern' => 'kirby-2fa/auth/code',
                'method' => 'POST',
                'auth' => false,
                'action' => function () {
                    $verify = (new Authenticator(get('email')))->verifyCode(get('code'));
                    return compact('verify');
                },
            ],
        ];
    },
];