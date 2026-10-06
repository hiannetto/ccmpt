<?php

namespace Core;

/**
 * Utilitário de leitura da requisição: unifica JSON e multipart/form-data.
 */
class Request {
    private static $body = null;

    public static function all() {
        if (self::$body === null) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $decoded = json_decode(file_get_contents('php://input'), true);
                self::$body = is_array($decoded) ? $decoded : [];
            } else {
                self::$body = $_POST;
            }
        }
        return self::$body;
    }

    public static function input($key, $default = null) {
        $all = self::all();
        if (!array_key_exists($key, $all)) {
            return $default;
        }
        $value = $all[$key];
        return is_string($value) ? trim($value) : $value;
    }

    public static function has($key) {
        return array_key_exists($key, self::all());
    }

    public static function query($key, $default = null) {
        return isset($_GET[$key]) && $_GET[$key] !== '' ? $_GET[$key] : $default;
    }

    public static function page() {
        return max(1, (int) self::query('page', 1));
    }

    public static function perPage($default = 12, $max = 100) {
        return min($max, max(1, (int) self::query('per_page', $default)));
    }

    /** Arquivo único enviado sem erro. */
    public static function file($key) {
        if (isset($_FILES[$key]) && !is_array($_FILES[$key]['name']) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            return $_FILES[$key];
        }
        return null;
    }

    /** Lista de arquivos de um campo múltiplo (ex.: photos[]). */
    public static function files($key) {
        if (!isset($_FILES[$key]) || !is_array($_FILES[$key]['name'])) {
            $single = self::file($key);
            return $single ? [$single] : [];
        }
        $list = [];
        foreach ($_FILES[$key]['name'] as $i => $name) {
            $list[] = [
                'name' => $name,
                'type' => $_FILES[$key]['type'][$i],
                'tmp_name' => $_FILES[$key]['tmp_name'][$i],
                'error' => $_FILES[$key]['error'][$i],
                'size' => $_FILES[$key]['size'][$i],
            ];
        }
        return $list;
    }

    /** Token Bearer enviado no cabeçalho Authorization. */
    public static function bearerToken() {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                if (strcasecmp($name, 'Authorization') === 0) {
                    $header = $value;
                }
            }
        }
        if (preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
            return $m[1];
        }
        return null;
    }

    /** Usuário autenticado (ou null). Funciona também em rotas públicas. */
    public static function user() {
        if (isset($_SERVER['AUTH_USER'])) {
            return $_SERVER['AUTH_USER'];
        }
        $token = self::bearerToken();
        if ($token) {
            $payload = JWT::decode($token);
            if ($payload) {
                $_SERVER['AUTH_USER'] = $payload;
                return $payload;
            }
        }
        return null;
    }
}
