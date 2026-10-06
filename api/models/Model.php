<?php

namespace Models;

use Core\Database;

abstract class Model {
    protected $db;
    protected $table;
    protected $softDeletes = false;

    public function __construct() {
        $this->db = Database::connection();
    }

    protected function query($sql, array $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function find($id) {
        $sql = "SELECT * FROM `{$this->table}` WHERE id = ?" . ($this->softDeletes ? ' AND deleted_at IS NULL' : '') . ' LIMIT 1';
        return $this->query($sql, [$id])->fetch() ?: null;
    }

    public function findBySlug($slug) {
        $sql = "SELECT * FROM `{$this->table}` WHERE slug = ?" . ($this->softDeletes ? ' AND deleted_at IS NULL' : '') . ' LIMIT 1';
        return $this->query($sql, [$slug])->fetch() ?: null;
    }

    /** Exclusão lógica: o registro permanece no banco. */
    public function softDelete($id) {
        return $this->query("UPDATE `{$this->table}` SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?", [$id])->rowCount() > 0;
    }

    /**
     * Paginação genérica.
     * @return array [itens, total]
     */
    protected function paginate($select, $from, array $where, array $params, $order, $page, $perPage) {
        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $total = (int) $this->query("SELECT COUNT(*) FROM $from $whereSql", $params)->fetchColumn();
        $offset = ($page - 1) * $perPage;
        $items = $this->query(
            "SELECT $select FROM $from $whereSql ORDER BY $order LIMIT " . (int) $perPage . ' OFFSET ' . (int) $offset,
            $params
        )->fetchAll();
        return [$items, $total];
    }

    public function lastId() {
        return (int) $this->db->lastInsertId();
    }
}
