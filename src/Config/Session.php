<?php

namespace App\Config;

class Session
{
    private static $started = false;

    public static function start()
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            // Secure session configuration
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Lax');

            // Use secure cookies in production
            if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
                ini_set('session.cookie_secure', 1);
            }

            // Session lifetime (strict: 1 hour default)
            $lifetime = (int) ($_ENV['SESSION_LIFETIME'] ?? 60) * 60;
            ini_set('session.gc_maxlifetime', $lifetime);
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
            self::$started = true;

            // Regenerate session ID periodically for security
            if (!isset($_SESSION['_created'])) {
                $_SESSION['_created'] = time();
            } elseif (time() - $_SESSION['_created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['_created'] = time();
            }
        }
    }

    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy()
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    public static function flash($key, $value = null)
    {
        self::start();
        if ($value === null) {
            $flashValue = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $flashValue;
        }
        $_SESSION['_flash'][$key] = $value;
    }

    public static function isLoggedIn()
    {
        return self::has('user_id');
    }

    public static function user()
    {
        return [
            'id' => self::get('user_id'),
            'name' => self::get('user_name'),
            'email' => self::get('user_email'),
            'role' => self::get('user_role')
        ];
    }
}
