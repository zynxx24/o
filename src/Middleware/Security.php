<?php

namespace App\Middleware;

use App\Config\Session;

class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken()
    {
        Session::start();
        if (!Session::has('csrf_token') || !Session::has('csrf_token_time')) {
            self::regenerateCsrfToken();
        }

        // Check if token has expired
        $lifetime = (int) ($_ENV['CSRF_TOKEN_LIFETIME'] ?? 3600);
        if (time() - Session::get('csrf_token_time', 0) > $lifetime) {
            self::regenerateCsrfToken();
        }

        return Session::get('csrf_token');
    }

    /**
     * Regenerate CSRF token
     */
    public static function regenerateCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        Session::set('csrf_token_time', time());
        return $token;
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken($token)
    {
        $storedToken = Session::get('csrf_token');
        if (!$storedToken || !$token) {
            return false;
        }
        return hash_equals($storedToken, $token);
    }

    /**
     * Get CSRF token input field for forms
     */
    public static function csrfField()
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Sanitize input string
     */
    public static function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email)
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Validate email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone number (Indonesian format)
     */
    public static function validatePhone($phone)
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        // Check for valid Indonesian phone format
        return preg_match('/^(\+62|62|0)[0-9]{8,13}$/', $phone);
    }

    /**
     * Sanitize integer
     */
    public static function sanitizeInt($value)
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize float
     */
    public static function sanitizeFloat($value)
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Hash password securely
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password)
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf besar';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf kecil';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung angka';
        }

        return $errors;
    }

    /**
     * Generate random token
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Rate limiting check (simple implementation)
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $decaySeconds = 60)
    {
        Session::start();
        $rateLimitKey = 'rate_limit_' . $key;
        $attempts = Session::get($rateLimitKey, []);

        // Clean old attempts
        $now = time();
        $attempts = array_filter($attempts, function ($timestamp) use ($now, $decaySeconds) {
            return ($now - $timestamp) < $decaySeconds;
        });

        if (count($attempts) >= $maxAttempts) {
            return false;
        }

        $attempts[] = $now;
        Session::set($rateLimitKey, $attempts);

        return true;
    }

    /**
     * XSS-safe output
     */
    public static function e($string)
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }

    // ========================================
    // AUTH TOKEN MANAGEMENT (Modern Security)
    // ========================================

    private static $tokenLifetime = 3600; // 1 hour in seconds
    private static $refreshWindow = 900;  // 15 minutes

    /**
     * Create auth token for user
     */
    public static function createAuthToken($db, $userId)
    {
        $token = self::generateToken(32); // 64 char hex
        $expiresAt = date('Y-m-d H:i:s', time() + self::$tokenLifetime);
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        // Invalidate old tokens for this user (optional: keep last 5)
        $stmt = $db->prepare("UPDATE auth_tokens SET is_valid = FALSE WHERE user_id = ? AND is_valid = TRUE");
        $stmt->execute([$userId]);

        // Create new token
        $stmt = $db->prepare("INSERT INTO auth_tokens (user_id, token, expires_at, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $token, $expiresAt, $ipAddress, $userAgent]);

        // Set secure cookie
        $cookieOptions = [
            'expires' => time() + self::$tokenLifetime,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ];

        if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
            $cookieOptions['secure'] = true;
        }

        setcookie('auth_token', $token, $cookieOptions);

        // Store token time in session for re-auth checks
        Session::set('auth_token_created', time());

        return $token;
    }

    /**
     * Validate auth token
     */
    public static function validateAuthToken($db)
    {
        $token = $_COOKIE['auth_token'] ?? null;
        if (!$token) {
            return false;
        }

        $stmt = $db->prepare("SELECT at.*, u.user_id, u.role, u.full_name, u.email 
                              FROM auth_tokens at 
                              JOIN users u ON at.user_id = u.user_id 
                              WHERE at.token = ? AND at.is_valid = TRUE AND at.expires_at > NOW()");
        $stmt->execute([$token]);
        $result = $stmt->fetch();

        if (!$result) {
            self::clearAuthCookie();
            return false;
        }

        // Optional: Check IP binding (can be strict or warn)
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? null;
        if ($result['ip_address'] && $result['ip_address'] !== $currentIp) {
            // IP changed - could be suspicious, invalidate token
            self::invalidateToken($db, $token);
            self::clearAuthCookie();
            return false;
        }

        // Refresh token if within refresh window
        $expiresAt = strtotime($result['expires_at']);
        $timeLeft = $expiresAt - time();
        if ($timeLeft < self::$refreshWindow) {
            self::refreshToken($db, $token);
        }

        // Update last activity
        $stmt = $db->prepare("UPDATE auth_tokens SET last_activity = NOW() WHERE token = ?");
        $stmt->execute([$token]);

        return $result;
    }

    /**
     * Refresh token expiry
     */
    public static function refreshToken($db, $token)
    {
        $newExpiry = date('Y-m-d H:i:s', time() + self::$tokenLifetime);
        $stmt = $db->prepare("UPDATE auth_tokens SET expires_at = ? WHERE token = ? AND is_valid = TRUE");
        $stmt->execute([$newExpiry, $token]);

        // Update cookie expiry
        $cookieOptions = [
            'expires' => time() + self::$tokenLifetime,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        setcookie('auth_token', $token, $cookieOptions);
    }

    /**
     * Invalidate token (logout)
     */
    public static function invalidateToken($db, $token = null)
    {
        $token = $token ?? ($_COOKIE['auth_token'] ?? null);
        if ($token) {
            $stmt = $db->prepare("UPDATE auth_tokens SET is_valid = FALSE WHERE token = ?");
            $stmt->execute([$token]);
        }
        self::clearAuthCookie();
    }

    /**
     * Clear auth cookie
     */
    private static function clearAuthCookie()
    {
        setcookie('auth_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    /**
     * Invalidate all tokens for user (for password change, security breach)
     */
    public static function invalidateAllUserTokens($db, $userId)
    {
        $stmt = $db->prepare("UPDATE auth_tokens SET is_valid = FALSE WHERE user_id = ?");
        $stmt->execute([$userId]);
        self::clearAuthCookie();
    }

    /**
     * Require recent authentication for sensitive actions
     * Returns true if auth is recent (within $maxAge seconds)
     */
    public static function requireRecentAuth($maxAge = 300) // 5 minutes default
    {
        $tokenCreated = Session::get('auth_token_created', 0);
        return (time() - $tokenCreated) < $maxAge;
    }

    /**
     * Get remaining token time in seconds
     */
    public static function getTokenTimeRemaining($db)
    {
        $token = $_COOKIE['auth_token'] ?? null;
        if (!$token)
            return 0;

        $stmt = $db->prepare("SELECT TIMESTAMPDIFF(SECOND, NOW(), expires_at) as remaining FROM auth_tokens WHERE token = ? AND is_valid = TRUE");
        $stmt->execute([$token]);
        $result = $stmt->fetch();

        return max(0, $result['remaining'] ?? 0);
    }
}
