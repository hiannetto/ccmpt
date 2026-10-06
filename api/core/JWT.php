<?php

namespace Core;

/**
 * JWT HS256 mínimo, sem dependências.
 */
class JWT {
    public static function encode(array $payload) {
        $payload['iat'] = time();
        $payload['exp'] = $payload['exp'] ?? time() + (int) Config::get('jwt_ttl', 43200);

        $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $body = self::base64UrlEncode(json_encode($payload));
        $signature = self::base64UrlEncode(hash_hmac('sha256', "$header.$body", self::secret(), true));

        return "$header.$body.$signature";
    }

    /** Retorna o payload se a assinatura for válida e o token não estiver expirado. */
    public static function decode($token) {
        $parts = explode('.', (string) $token);
        if (count($parts) !== 3) {
            return false;
        }
        [$header, $body, $signature] = $parts;

        $valid = self::base64UrlEncode(hash_hmac('sha256', "$header.$body", self::secret(), true));
        if (!hash_equals($valid, $signature)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($body), true);
        if (!is_array($payload) || (isset($payload['exp']) && $payload['exp'] < time())) {
            return false;
        }
        return $payload;
    }

    private static function secret() {
        return Config::get('jwt_secret');
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }
}
