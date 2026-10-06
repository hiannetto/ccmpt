<?php

namespace Core;

class Response {
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error($message, $status = 400, $fields = null) {
        $payload = ['error' => $message];
        if ($fields) {
            $payload['fields'] = $fields;
        }
        self::json($payload, $status);
    }

    public static function paginated($items, $total, $page, $perPage) {
        self::json([
            'data' => $items,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => (int) $total,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }
}
