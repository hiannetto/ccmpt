<?php

namespace Models;

use Core\ImageService;

class MemorialItem extends Model {
    protected $table = 'memorial_items';
    protected $softDeletes = true;

    /** Estados de conservação aceitos na ficha técnica. */
    const CONSERVATION = [
        'otimo' => 'Ótimo',
        'bom' => 'Bom',
        'regular' => 'Regular',
        'ruim' => 'Ruim / precisa de restauro',
    ];

    /** Colunas gravadas pelo formulário de catalogação. */
    const FIELDS = [
        'title', 'dating_label', 'year', 'inventory_number', 'historical_description',
        'material', 'dimensions', 'provenance', 'conservation_state',
        'main_image_url', 'main_image_caption',
    ];

    /**
     * Filtros: q (texto) e categories (ids). Entre tipos diferentes aplica E;
     * dentro do mesmo tipo aplica OU (ex.: década 1970 OU 1980, E tipo "Documento").
     */
    public function search(array $f, $page, $perPage) {
        $where = ['m.deleted_at IS NULL'];
        $params = [];

        if (!empty($f['q'])) {
            $like = '%' . $f['q'] . '%';
            $where[] = '(m.title LIKE ? OR m.historical_description LIKE ? OR m.inventory_number LIKE ?
                         OR m.dating_label LIKE ? OR m.material LIKE ? OR m.provenance LIKE ?)';
            array_push($params, $like, $like, $like, $like, $like, $like);
        }

        if (!empty($f['categories'])) {
            $groups = (new Category())->groupIdsByType($f['categories']);
            foreach ($groups as $ids) {
                $in = implode(',', array_fill(0, count($ids), '?'));
                $where[] = "EXISTS (SELECT 1 FROM memorial_item_category mic WHERE mic.item_id = m.id AND mic.category_id IN ($in))";
                array_push($params, ...$ids);
            }
        }

        switch ($f['sort'] ?? '') {
            case 'title':   $order = 'm.title ASC'; break;
            case 'year':    $order = 'm.year IS NULL, m.year ASC, m.title ASC'; break;
            case 'updated': $order = 'm.updated_at DESC'; break;
            default:        $order = 'm.created_at DESC';
        }

        [$items, $total] = $this->paginate(
            'm.id, m.title, m.dating_label, m.year, m.inventory_number, m.historical_description,
             m.main_image_url, m.conservation_state, m.created_at, m.updated_at,
             (SELECT COUNT(*) FROM memorial_images mi WHERE mi.item_id = m.id AND mi.deleted_at IS NULL) AS images_count',
            'memorial_items m', $where, $params, $order, $page, $perPage
        );

        $categoriesByItem = $this->categoriesFor(array_column($items, 'id'));
        $items = array_map(function ($item) use ($categoriesByItem) {
            $item = self::format($item);
            $text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($item['historical_description']), ENT_QUOTES, 'UTF-8')));
            $item['excerpt'] = mb_strlen($text) > 160 ? rtrim(mb_substr($text, 0, 160)) . '...' : $text;
            unset($item['historical_description']);
            $item['categories'] = $categoriesByItem[$item['id']] ?? [];
            return $item;
        }, $items);

        return [$items, $total];
    }

    public function detail($id) {
        $item = $this->find($id);
        if (!$item) {
            return null;
        }
        $item = self::format($item);
        $item['categories'] = $this->categoriesFor([$item['id']])[$item['id']] ?? [];
        $item['images'] = array_map([self::class, 'formatImage'], $this->query(
            'SELECT id, image_url, caption FROM memorial_images WHERE item_id = ? AND deleted_at IS NULL ORDER BY id',
            [$id]
        )->fetchAll());
        return $item;
    }

    public function create(array $d) {
        $cols = array_values(array_filter(self::FIELDS, function ($c) use ($d) { return array_key_exists($c, $d); }));
        $this->query(
            'INSERT INTO memorial_items (' . implode(', ', $cols) . ') VALUES (' . implode(', ', array_fill(0, count($cols), '?')) . ')',
            array_map(function ($c) use ($d) { return $d[$c]; }, $cols)
        );
        return $this->lastId();
    }

    public function update($id, array $d) {
        $cols = array_values(array_filter(self::FIELDS, function ($c) use ($d) { return array_key_exists($c, $d); }));
        $set = implode(', ', array_map(function ($c) { return "$c = ?"; }, $cols));
        $params = array_map(function ($c) use ($d) { return $d[$c]; }, $cols);
        $params[] = $id;
        $this->query("UPDATE memorial_items SET $set WHERE id = ?", $params);
    }

    /** Outro item ativo com o mesmo nº de inventário (evita duplicidade de tombo). */
    public function findByInventory($number, $exceptId = null) {
        return $this->query(
            'SELECT id, title FROM memorial_items WHERE inventory_number = ? AND deleted_at IS NULL AND id <> ? LIMIT 1',
            [$number, (int) $exceptId]
        )->fetch() ?: null;
    }

    public function syncCategories($id, array $categoryIds) {
        $this->query('DELETE FROM memorial_item_category WHERE item_id = ?', [$id]);
        foreach (array_unique(array_map('intval', $categoryIds)) as $cid) {
            if ($cid > 0) {
                $this->query('INSERT IGNORE INTO memorial_item_category (item_id, category_id) VALUES (?, ?)', [$id, $cid]);
            }
        }
    }

    // ---------- Fotos extras (galeria da peça) ----------

    public function addImage($id, $url, $caption = null) {
        $this->query('INSERT INTO memorial_images (item_id, image_url, caption) VALUES (?, ?, ?)', [$id, $url, $caption]);
        return $this->lastId();
    }

    public function findImage($id, $imageId) {
        return $this->query(
            'SELECT id, item_id, image_url, caption FROM memorial_images WHERE id = ? AND item_id = ? AND deleted_at IS NULL LIMIT 1',
            [$imageId, $id]
        )->fetch() ?: null;
    }

    public function updateImageCaption($id, $imageId, $caption) {
        $this->query('UPDATE memorial_images SET caption = ? WHERE id = ? AND item_id = ?', [$caption, $imageId, $id]);
    }

    public function updateMainCaption($id, $caption) {
        $this->query('UPDATE memorial_items SET main_image_caption = ? WHERE id = ?', [$caption, $id]);
    }

    /** Exclusão lógica: o registro e o arquivo físico são preservados. */
    public function removeImage($id, $imageId) {
        return $this->query(
            'UPDATE memorial_images SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND item_id = ? AND deleted_at IS NULL',
            [$imageId, $id]
        )->rowCount() > 0;
    }

    /**
     * Promove uma foto da galeria a principal: a foto principal atual
     * ocupa o lugar dela na galeria (troca, sem perder nenhuma imagem).
     */
    public function setMainImage($id, $imageId) {
        $item = $this->find($id);
        $image = $this->findImage($id, $imageId);
        if (!$item || !$image) {
            return false;
        }
        $this->db->beginTransaction();
        try {
            $this->query(
                'UPDATE memorial_items SET main_image_url = ?, main_image_caption = ? WHERE id = ?',
                [$image['image_url'], $image['caption'], $id]
            );
            $this->query(
                'UPDATE memorial_images SET image_url = ?, caption = ? WHERE id = ?',
                [$item['main_image_url'], $item['main_image_caption'], $imageId]
            );
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
        return true;
    }

    private function categoriesFor(array $ids) {
        if (!$ids) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->query(
            "SELECT mic.item_id, c.id, c.name, c.slug, c.type FROM memorial_item_category mic
             JOIN categories c ON c.id = mic.category_id WHERE mic.item_id IN ($in) ORDER BY c.type, c.name",
            array_values($ids)
        )->fetchAll();
        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r['item_id']][] = ['id' => (int) $r['id'], 'name' => $r['name'], 'slug' => $r['slug'], 'type' => $r['type']];
        }
        return $map;
    }

    public static function format(array $item) {
        $item['id'] = (int) $item['id'];
        if (array_key_exists('year', $item)) {
            $item['year'] = $item['year'] === null ? null : (int) $item['year'];
        }
        if (array_key_exists('images_count', $item)) {
            $item['images_count'] = (int) $item['images_count'];
        }
        $item['main_thumbnail_url'] = ImageService::thumbFor($item['main_image_url']);
        unset($item['deleted_at']);
        return $item;
    }

    public static function formatImage(array $img) {
        return [
            'id' => (int) $img['id'],
            'image_url' => $img['image_url'],
            'thumbnail_url' => ImageService::thumbFor($img['image_url']),
            'caption' => $img['caption'],
        ];
    }
}
