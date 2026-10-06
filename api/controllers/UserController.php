<?php

namespace Controllers;

use Core\Input;
use Core\Request;
use Core\Response;
use Models\User;

/**
 * Gestão da equipe (somente Administrador).
 */
class UserController {
    private $users;

    public function __construct() {
        $this->users = new User();
    }

    public function index() {
        Response::json($this->users->all());
    }

    public function store() {
        Input::require(['name' => 'Nome', 'email' => 'E-mail', 'password' => 'Senha', 'role' => 'Perfil']);
        $data = $this->validated();
        if (mb_strlen($data['password']) < 8) {
            Response::error('A senha deve ter pelo menos 8 caracteres.', 422, ['password' => 'Mínimo de 8 caracteres.']);
        }
        $id = $this->users->create($data);
        Response::json(User::publicFields($this->users->find($id)), 201);
    }

    public function update($id) {
        $user = $this->users->find($id) ?: Response::error('Usuário não encontrado.', 404);
        Input::require(['name' => 'Nome', 'email' => 'E-mail', 'role' => 'Perfil']);
        $data = $this->validated($id);

        if ($data['password'] && mb_strlen($data['password']) < 8) {
            Response::error('A senha deve ter pelo menos 8 caracteres.', 422, ['password' => 'Mínimo de 8 caracteres.']);
        }
        if ($user['role'] === 'admin' && $data['role'] !== 'admin' && $this->users->countAdmins() <= 1) {
            Response::error('O sistema precisa ter pelo menos um administrador.', 422);
        }

        $this->users->update($id, $data);
        Response::json(User::publicFields($this->users->find($id)));
    }

    public function destroy($id) {
        $user = $this->users->find($id) ?: Response::error('Usuário não encontrado.', 404);
        if ((int) Request::user()['id'] === (int) $id) {
            Response::error('Você não pode remover o seu próprio acesso.', 422);
        }
        if ($user['role'] === 'admin' && $this->users->countAdmins() <= 1) {
            Response::error('O sistema precisa ter pelo menos um administrador.', 422);
        }
        $this->users->softDelete($id);
        Response::json(['message' => 'Acesso removido.']);
    }

    private function validated($ignoreId = null) {
        $email = mb_strtolower(Request::input('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Informe um e-mail válido.', 422, ['email' => 'E-mail inválido.']);
        }
        if ($this->users->emailTaken($email, $ignoreId)) {
            Response::error('Este e-mail já está cadastrado.', 422, ['email' => 'E-mail já cadastrado.']);
        }
        $role = Request::input('role');
        if (!in_array($role, ['admin', 'editor'], true)) {
            Response::error('Perfil inválido.', 422, ['role' => 'Perfil inválido.']);
        }
        return [
            'name' => Request::input('name'),
            'email' => $email,
            'role' => $role,
            'password' => (string) Request::input('password', ''),
        ];
    }
}
