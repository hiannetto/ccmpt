<?php

namespace Core;

/**
 * Utilitários de validação e limpeza de dados de entrada.
 */
class Input {
    public static function bool($value, $default = true) {
        if ($value === null || $value === '') {
            return $default ? 1 : 0;
        }
        return in_array($value, [true, 1, '1', 'true', 'on', 'sim'], true) ? 1 : 0;
    }

    /** Converte "2026-10-05T19:30" ou "2026-10-05 19:30" em DATETIME. */
    public static function datetime($value) {
        if (!$value) {
            return null;
        }
        $ts = strtotime(str_replace('T', ' ', $value));
        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }

    /** Lista de ids vinda como array, "1,2,3" ou JSON "[1,2]". */
    public static function ids($value) {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : explode(',', $value);
        }
        if (!is_array($value)) {
            return [];
        }
        return array_values(array_filter(array_map('intval', $value), function ($v) { return $v > 0; }));
    }

    /**
     * Limpeza do HTML vindo do editor de texto: remove scripts, iframes,
     * atributos de evento (onclick...) e links javascript:.
     * O frontend também sanitiza na exibição (defesa em camadas).
     */
    public static function html($html) {
        $html = (string) $html;
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button)[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button)[^>]*/?>#is', '', $html);
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*\2/i', '$1="#"', $html);
        return trim($html);
    }

    /** Verifica se um HTML tem conteúdo de verdade (texto ou imagem). */
    public static function htmlHasContent($html) {
        return trim(strip_tags((string) $html, '<img>')) !== '';
    }

    /** Valida campos obrigatórios e responde 422 com a lista de erros. */
    public static function require(array $rules) {
        $errors = [];
        foreach ($rules as $field => $label) {
            $v = Request::input($field);
            if ($v === null || $v === '' || (is_array($v) && !$v)) {
                $errors[$field] = "Preencha o campo \"$label\".";
            }
        }
        if ($errors) {
            Response::error(reset($errors), 422, $errors);
        }
    }
}
