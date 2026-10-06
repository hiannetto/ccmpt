<?php

namespace Core;

class Config {
    private static $config = null;

    public static function get($key, $default = null) {
        if (self::$config === null) {
            self::$config = require dirname(__DIR__) . '/config.php';
        }
        $value = self::$config;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }
}
