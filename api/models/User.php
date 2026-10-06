<?php

namespace Models;

class User extends Model {
    protected $table = 'users';
    protected $softDeletes = true;

    public function findByEmail($email) {
        return $this->query('SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1', [$email])->fetch() ?: null;
    }

    public function emailTaken($email, $ignoreId = null) {
        $sql = 'SELECT id FROM users WHERE email = ? AND deleted_at IS NULL' . ($ignoreId ? ' AND id <> ?' : '');
        return (bool) $this->query($sql, $ignoreId ? [$email, $ignoreId] : [$email])->fetch();
    }

    public function all() {
        return $this->query('SELECT id, name, email, role, created_at FROM users WHERE deleted_at IS NULL ORDER BY name')->fetchAll();
    }

    public function countAdmins() {
        return (int) $this->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND deleted_at IS NULL")->fetchColumn();
    }

    public function create(array $d) {
        $this->query(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)',
            [$d['name'], $d['email'], password_hash($d['password'], PASSWORD_DEFAULT), $d['role']]
        );
        return $this->lastId();
    }

    public function update($id, array $d) {
        $this->query('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?', [$d['name'], $d['email'], $d['role'], $id]);
        if (!empty($d['password'])) {
            $this->query('UPDATE users SET password = ? WHERE id = ?', [password_hash($d['password'], PASSWORD_DEFAULT), $id]);
        }
    }

    public static function publicFields(array $u) {
        return ['id' => (int) $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']];
    }
}
