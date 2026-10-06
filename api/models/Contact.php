<?php

namespace Models;

class Contact extends Model {
    protected $table = 'contacts';

    public function search($status, $page, $perPage) {
        $where = [];
        $params = [];
        if (in_array($status, ['pendente', 'atendido'], true)) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        [$items, $total] = $this->paginate('*', 'contacts', $where, $params, "status = 'pendente' DESC, created_at DESC", $page, $perPage);
        return [array_map([self::class, 'format'], $items), $total];
    }

    public function counts() {
        $rows = $this->query('SELECT status, COUNT(*) AS total FROM contacts GROUP BY status')->fetchAll();
        $out = ['pendente' => 0, 'atendido' => 0];
        foreach ($rows as $r) {
            $out[$r['status']] = (int) $r['total'];
        }
        return $out;
    }

    public function create(array $d) {
        $this->query(
            "INSERT INTO contacts (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, 'pendente')",
            [$d['name'], $d['email'], $d['phone'], $d['subject'], $d['message']]
        );
        return $this->lastId();
    }

    public function updateStatus($id, $status) {
        $this->query('UPDATE contacts SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function format(array $c) {
        $c['id'] = (int) $c['id'];
        return $c;
    }
}
