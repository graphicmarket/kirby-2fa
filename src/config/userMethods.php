<?php

namespace graphicmarket\kirby2fa;

return [

    'has2FA' => function (): bool {
        return (new Register())->exist($this->id());
    },

    'secret2FA' => function (): ?string {
        $data = (new Register())->get($this->id());
        return $data ? $data['secret'] : $data ;
    },

    'enable2FA' => function (string $secret) {

        (new Register())->add([
            'user_id' => $this->id(),
            'secret' => $secret,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        (new Authenticator())->removeFromCache();

        return $this;
    },

    'disable2FA' => function (): bool {
        return (new Register())->delete($this->id());
    },
];