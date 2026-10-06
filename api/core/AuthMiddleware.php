<?php

namespace Core;

/**
 * Exige um token válido (Administrador ou Editor).
 */
class AuthMiddleware {
    public function handle() {
        $user = Request::user();
        if (!$user) {
            Response::error('Sessão expirada ou inválida. Faça login novamente.', 401);
        }
    }
}
