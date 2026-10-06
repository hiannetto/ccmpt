<?php

namespace Models;

use Core\ImageService;

class MemorialItem extends Model {
    protected $table = 'memorial_items';
    protected $softDeletes = true;

    /**
     * Filtros: q (texto) e categories (ids). Entre tipos diferentes aplica E;
     * dentro do mesmo tipo aplica OU (ex.: década 1970 OU 1980, E tipo "Documento").
     */
    public function search(array $f, $page, $perPage) {
        $where = ['m.deleted_at IS NULL'];
        $params = [];

        if (!empty($f['q'])) {
            $where[] = '(m.title LIKE ? OR m.historical_description LIKE ?)';
            $params[] = '%' . $f['q'] . '%';
            $params[] = '%' . $f['q'] . '%';
        }

        if (!empty($f['categories'])) {
            $groups = (new Category())->groupIdsByType($f['categories']);
            foreach ($groups as $ids) {
                $in = implode(',', array_fill(0, count($ids), '?'));
                $where[] = "EXISTS (SELECT 1 FROM memorial_item_category mic WHERE mic.item_id = m.id AND mic.category_id IN ($in))";
                array_push($params, ...$ids);
            }
        }

        $order = ($f['sort'] ?? '') === 'title' ? 'm.title ASC' : 'm.created_at DESC';

        [$items, $total] = $this->paginate(
            'm.id, m.title, m.historical_description, m.main_image_url, m.created_at, m.updated_at',
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
        $item['images'] = array_map(function ($img) {
            return [
                'id' => (int) $img['id'],
                'image_url' => $img['image_url'],
                'thumbnail_url' => ImageService::thumbFor($img['image_url']),
            ];
        }, $this->query('SELECT id, image_url FROM memorial_images WHERE item_id = ? ORDER BY id', [$id])->fetchAll());
        return $item;
    }

    public function create(array $d) {
        $this->query(
            'INSERT INTO memorial_items (title, historical_description, main_image_url) VALUES (?, ?, ?)',
            [$d['title'], $d['historical_description'], $d['main_image_url']]
        );
        return $this->lastId();
    }

    public function update($id, array $d) {
        $this->query(
            'UPDATE memorial_items SET title = ?, historical_description = ?, main_image_url = ? WHERE id = ?',
            [$d['title'], $d['historical_description'], $d['main_image_url'], $id]
        );
    }

    public function syncCategories($id, array $categoryIds) {
        $this->query('DELETE FROM memorial_item_category WHERE item_id = ?', [$id]);
        foreach (array_unique(array_map('intval', $categoryIds)) as $cid) {
            if ($cid > 0) {
                $this->query('INSERT IGNORE INTO memorial_item_category (item_id, category_id) VALUES (?, ?)', [$id, $cid]);
            }
        }
    }

    public function addImage($id, $url) {
        $this->query('INSERT INTO memorial_images (item_id, image_url) VALUES (?, ?)', [$id, $url]);
    }

    public function removeImage($id, $imageId) {
        return $this->query('DELETE FROM memorial_images WHERE id = ? AND item_id = ?', [$imageId, $id])->rowCount() > 0;
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
        $item['main_thumbnail_url'] = ImageService::thumbFor($item['main_image_url']);
        return $item;
    }
}
