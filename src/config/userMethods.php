<?php

namespace graphicmarket\kirby2fa;

return [

    /**
     * Verify if the user has a tfa method
     */
    'has2FA' => function (): bool {
        return (new Register())->exist($this->id());
    },

    /**
     * Get the user secret
     */
    'secret2FA' => function (): ?string {
        $data = (new Register())->get($this->id());
        return $data ? $data['secret'] : $data ;
    },

    /**
     * Enable tfa for the user
     */
    'enable2FA' => function (string $secret) {

        (new Register())->add([
            'user_id' => $this->id(),
            'secret' => $secret,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        (new TwoFactorAuthentication())->removeFromCache();

        return $this;
    },

    /**
     * Disable tfa for the user
     */
    'disable2FA' => function (): bool {
        return (new Register())->delete($this->id());
    },
];