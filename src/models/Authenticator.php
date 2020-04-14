<?php

namespace graphicmarket\kirby2fa;

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

/**
 * The Authenticator class try to cover the basic funtionalities
 * To work with 2fa and the kirby's users
 *
 * @author    Ronald Torres <ronald@graphic.market>
 * @license   https://opensource.org/licenses/MIT
 */

class Authenticator {

    /**
     * The TwoFactorAuth instance
     *
     * @var RobThree\Auth\TwoFactorAuth
     */
    private $tfa;

    /**
     * The user secret
     *
     * @var string
     */
    private $secret;

    /**
     * Temporary File cache
     *
     * @var Kirby\Cache\Cache
     */
    private $cache;

    /**
     * The authenticated user or that's trying to login
     *
     * @var [type]
     */
    private $user;

    public function __construct(?string $email = null) {

        $this->tfa = new TwoFactorAuth(option('graphicmarket.kirby-2fa.issuer'));
        $this->cache = kirby()->cache('graphicmarket.kirby-2fa.default');
        $this->user = kirby()->user($email);

    }

    /**
     * Get the secret and cache it while the user verify it through the code
     *
     * @return string
     */
    public function getSecret(): string {

        if ($this->secret) {
            return $this->secret;
        }

        // Get the secret from the databae
        if ($r = $this->user->secret2FA()) {
            return $r;
        }

        // Get the secret from file cache
        if ($r = $this->cache->get($this->user->id())) {
            return $this->secret = $r;
        }

        // Create a secret of 160 bits as a
        $this->secret = $this->tfa->createSecret(160);

        // Save the secret for 2 min that lets the user to scan the code
        $this->cache->set($this->user->id(), $this->secret, 2);

        return $this->secret;
    }

    public function getQRCode(): string {
        return $this->tfa->getQRCodeImageAsDataUri($this->user->email(), $this->getSecret());
    }

    public function getCode(): string {
        return $this->tfa->getCode($this->getSecret());
    }

    public function verifyCode(string $code): bool {
        return $this->tfa->verifyCode($this->getSecret(), $code);
    }

    public function removeFromCache(): bool {
        return $this->cache->remove($this->user->id());
    }

    public function checkTimeCompatibility(): string {
        try {
            $this->tfa->ensureCorrectTime();
            return 'Your hosts time seems to be correct within margin.';
        } catch (TwoFactorAuthException $ex) {
            return 'Warning: Your hosts time seems to be off: ' . $ex->getMessage();
        }
    }

}