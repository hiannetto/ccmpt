<?php

namespace Core;

/**
 * Restringe a rota ao perfil Administrador.
 * Deve ser usado sempre depois do AuthMiddleware.
 */
class AdminMiddleware {
    public function handle() {
        $user = Request::user();
        if (!$user) {
            Response::error('Sessão expirada ou inválida. Faça login novamente.', 401);
        }
        if (($user['role'] ?? '') !== 'admin') {
            Response::error('Acesso restrito a administradores.', 403);
        }
    }
}
