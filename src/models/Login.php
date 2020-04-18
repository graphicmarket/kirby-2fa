<?php

namespace graphicmarket\kirby2fa;

use Kirby\Cache\Cache;
use Kirby\Cms\Dir;
use Kirby\Cms\User;
use Kirby\Exception\Exception;
use Kirby\Exception\NotFoundException;
use Kirby\Exception\PermissionException;

/**
 * Handle Login methods
 *
 * @author Ronald Torres <ronald@graphic.market>
 */
class Login {

    /**
     * Cache to store temp auth files
     *
     * @var \Kirby\Cache\Cache
     */
    private static $cache = null;

    /**
     * Validate the user and verify if need 2fa
     *
     * @return void
     */
    public static function auth() {

        static::validateToken();

        try {
            // Validate if the user data it's ok
            $user = kirby()->auth()->validatePassword(get('email'), get('password'));

            // Verify 2fa
            if ($user->has2FA()) {

                $hash = uniqid();

                // Save the user id in a temporary file for 2fa authentication
                static::cache()->set("login_{$hash}", $user->id(), 2);

                // 202 means the request is accepted but a step is required (2fa)
                return [
                    'code' => 202,
                    'status' => 'ok',
                    'tfa_session_id' => $hash,
                ];
            }

            // Authenticate
            return static::signInUser($user);

        } catch (Exception $e) {
        }

        throw new PermissionException('Invalid email or password');
    }

    /**
     * Validate 2fa code and get user id from temporary file
     *
     * @return array
     */
    public static function auth2FA() {

        static::validateToken();

        // get the id from the temporary file
        $user_id = static::cache()->get('login_' . get('tfa_session_id'));

        // if the id is null means the session has expired.
        if (!$user_id) {
            throw new Exception('Session expired, reload the page.');
        }

        // get the user from id
        $user = kirby()->user($user_id);

        if (!$user) {
            throw new NotFoundException();
        }

        $isValid = (new TwoFactorAuthentication($user))->verifyCode(get('code'));

        if ($isValid) {

            // Delete the expired files
            static::removeExpired();

            // Authenticate
            return static::signInUser($user);
        }

        kirby()->auth()->track($user->email());
        throw new PermissionException('Invalid code');

    }

    /**
     * Get the cache
     *
     * @return \Kirby\Cache\Cache
     */
    public static function cache(): Cache {
        return static::$cache ?: static::$cache = kirby()->cache('graphicmarket.kirby-2fa');
    }

    /**
     * Authenticate the user
     *
     * @param \Kirby\Cms\User $user
     * @return array
     */
    public static function signInUser(User $user): array{

        $user->loginPasswordless([
            'createMode' => 'cookie',
            'long' => get('long') === true,
        ]);

        return [
            'code' => 200,
            'status' => 'ok',
            'user' => kirby()->api()->resolve($user)->view('auth')->toArray(),
        ];
    }

    /**
     * Remove all the expired caché files
     *
     * @return void
     */
    public static function removeExpired() {
        try {
            $opts = static::cache()->options();
            $dir = $opts['root'] .= '/' . $opts['prefix'];
            $files = Dir::files($dir);

            foreach ($files as $f) {
                $name = str_replace('.cache', '', $f);
                if (static::cache()->expired($name)) {
                    static::cache()->remove($name);
                }
            }
        } catch (\Throwable $th) {

        }
    }

    /**
     * Validate csrf token for the session
     *
     * @return void
     */
    public static function validateToken() {

        $auth = kirby()->auth();

        // csrf token check
        if ($auth->type() === 'session' && $auth->csrf() === false) {
            throw new \InvalidArgumentException('Invalid CSRF token');
        }

    }

}
