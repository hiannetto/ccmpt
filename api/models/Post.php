<?php

namespace Models;

use Core\ImageService;

class Post extends Model {
    protected $table = 'posts';
    protected $softDeletes = true;

    /**
     * Filtros: type (news|event), q, when (upcoming|past), published_only
     */
    public function search(array $f, $page, $perPage) {
        $where = ['deleted_at IS NULL'];
        $params = [];

        if (!empty($f['published_only'])) {
            $where[] = 'is_published = 1';
        }
        if (!empty($f['type']) && in_array($f['type'], ['news', 'event'], true)) {
            $where[] = 'type = ?';
            $params[] = $f['type'];
        }
        if (!empty($f['q'])) {
            $where[] = '(title LIKE ? OR content LIKE ?)';
            $params[] = '%' . $f['q'] . '%';
            $params[] = '%' . $f['q'] . '%';
        }

        $order = 'created_at DESC';
        if (($f['when'] ?? '') === 'upcoming') {
            $where[] = "type = 'event' AND event_date >= CURRENT_DATE";
            $order = 'event_date ASC';
        } elseif (($f['when'] ?? '') === 'past') {
            $where[] = "type = 'event' AND event_date < CURRENT_DATE";
            $order = 'event_date DESC';
        }

        [$items, $total] = $this->paginate(
            'id, type, title, slug, content, cover_image_url, event_date, is_published, created_at, updated_at',
            'posts', $where, $params, $order, $page, $perPage
        );

        return [array_map([self::class, 'summary'], $items), $total];
    }

    public function create(array $d) {
        $this->query(
            'INSERT INTO posts (type, title, slug, content, cover_image_url, event_date, is_published) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$d['type'], $d['title'], $d['slug'], $d['content'], $d['cover_image_url'], $d['event_date'], $d['is_published']]
        );
        return $this->lastId();
    }

    public function update($id, array $d) {
        $this->query(
            'UPDATE posts SET type = ?, title = ?, slug = ?, content = ?, cover_image_url = ?, event_date = ?, is_published = ? WHERE id = ?',
            [$d['type'], $d['title'], $d['slug'], $d['content'], $d['cover_image_url'], $d['event_date'], $d['is_published'], $id]
        );
    }

    /** Versão para listagens: sem o conteúdo completo, com resumo. */
    public static function summary(array $p) {
        $text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($p['content']), ENT_QUOTES, 'UTF-8')));
        $p['excerpt'] = mb_strlen($text) > 220 ? rtrim(mb_substr($text, 0, 220)) . '...' : $text;
        unset($p['content']);
        return self::format($p);
    }

    public static function format(array $p) {
        $p['id'] = (int) $p['id'];
        $p['is_published'] = (bool) $p['is_published'];
        $p['cover_thumbnail_url'] = ImageService::thumbFor($p['cover_image_url']);
        return $p;
    }
}
