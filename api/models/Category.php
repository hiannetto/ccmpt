<?php

namespace Models;

class Category extends Model {
    protected $table = 'categories';

    /** Tipos de classificação usados nos filtros do Acervo. */
    const TYPES = [
        'decada' => 'Década',
        'categoria' => 'Categoria',
        'tipo_objeto' => 'Tipo de objeto',
    ];

    public function all($withCounts = false) {
        $sql = $withCounts
            ? 'SELECT c.*, (SELECT COUNT(*) FROM memorial_item_category mic
                   JOIN memorial_items m ON m.id = mic.item_id AND m.deleted_at IS NULL
                   WHERE mic.category_id = c.id) AS items_count
               FROM categories c ORDER BY c.type, c.name'
            : 'SELECT * FROM categories ORDER BY type, name';
        return array_map(function ($c) {
            $c['id'] = (int) $c['id'];
            if (isset($c['items_count'])) {
                $c['items_count'] = (int) $c['items_count'];
            }
            return $c;
        }, $this->query($sql)->fetchAll());
    }

    public function create($name, $slug, $type) {
        $this->query('INSERT INTO categories (name, slug, type) VALUES (?, ?, ?)', [$name, $slug, $type]);
        return $this->lastId();
    }

    public function update($id, $name, $slug, $type) {
        $this->query('UPDATE categories SET name = ?, slug = ?, type = ? WHERE id = ?', [$name, $slug, $type, $id]);
    }

    public function delete($id) {
        return $this->query('DELETE FROM categories WHERE id = ?', [$id])->rowCount() > 0;
    }

    /** Agrupa ids de categorias por tipo: [tipo => [ids]] */
    public function groupIdsByType(array $ids) {
        if (!$ids) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->query("SELECT id, type FROM categories WHERE id IN ($in)", array_values($ids))->fetchAll();
        $groups = [];
        foreach ($rows as $r) {
            $groups[$r['type'] ?: 'outros'][] = (int) $r['id'];
        }
        return $groups;
    }
}
