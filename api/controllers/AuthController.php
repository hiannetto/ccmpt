<?php

namespace Controllers;

use Core\Input;
use Core\JWT;
use Core\Request;
use Core\Response;
use Models\User;

class AuthController {
    public function login() {
        Input::require(['email' => 'E-mail', 'password' => 'Senha']);

        $users = new User();
        $user = $users->findByEmail(Request::input('email'));

        if (!$user || !password_verify(Request::input('password'), $user['password'])) {
            Response::error('E-mail ou senha incorretos.', 401);
        }

        $token = JWT::encode([
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        Response::json(['token' => $token, 'user' => User::publicFields($user)]);
    }

    public function me() {
        $auth = Request::user();
        $user = (new User())->find($auth['id']);
        if (!$user) {
            Response::error('Usuário não encontrado.', 401);
        }
        Response::json(User::publicFields($user));
    }

    /** O próprio usuário altera nome e senha. */
    public function updateMe() {
        $auth = Request::user();
        $users = new User();
        $user = $users->find($auth['id']);

        Input::require(['name' => 'Nome']);
        $password = Request::input('password');
        if ($password) {
            if (!password_verify((string) Request::input('current_password'), $user['password'])) {
                Response::error('A senha atual não confere.', 422, ['current_password' => 'A senha atual não confere.']);
            }
            if (mb_strlen($password) < 8) {
                Response::error('A nova senha deve ter pelo menos 8 caracteres.', 422, ['password' => 'Mínimo de 8 caracteres.']);
            }
        }

        $users->update($user['id'], [
            'name' => Request::input('name'),
            'email' => $user['email'],
            'role' => $user['role'],
            'password' => $password,
        ]);
        Response::json(['message' => 'Dados atualizados.', 'user' => User::publicFields($users->find($user['id']))]);
    }

    public function ping() {
        Response::json(['message' => 'pong']);
    }
}
