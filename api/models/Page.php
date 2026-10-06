<?php

namespace Models;

class Page extends Model {
    protected $table = 'pages';

    /** Endereços já usados pelo site e que não podem virar página estática. */
    const RESERVED_SLUGS = [
        'admin', 'api', 'uploads', 'assets', 'noticias', 'eventos', 'acervo',
        'arquivo-fotografico', 'galerias', 'contato', 'login', 'home', 'inicio',
    ];

    public function all($publishedOnly) {
        $sql = 'SELECT id, title, slug, is_published, show_in_menu, updated_at FROM pages'
            . ($publishedOnly ? ' WHERE is_published = 1' : '')
            . ' ORDER BY title';
        return array_map([self::class, 'format'], $this->query($sql)->fetchAll());
    }

    public function create(array $d) {
        $this->query(
            'INSERT INTO pages (title, slug, content, is_published, show_in_menu) VALUES (?, ?, ?, ?, ?)',
            [$d['title'], $d['slug'], $d['content'], $d['is_published'], $d['show_in_menu']]
        );
        return $this->lastId();
    }

    public function update($id, array $d) {
        $this->query(
            'UPDATE pages SET title = ?, slug = ?, content = ?, is_published = ?, show_in_menu = ? WHERE id = ?',
            [$d['title'], $d['slug'], $d['content'], $d['is_published'], $d['show_in_menu'], $id]
        );
    }

    public function delete($id) {
        return $this->query('DELETE FROM pages WHERE id = ?', [$id])->rowCount() > 0;
    }

    public static function format(array $p) {
        $p['id'] = (int) $p['id'];
        $p['is_published'] = (bool) $p['is_published'];
        $p['show_in_menu'] = (bool) $p['show_in_menu'];
        return $p;
    }
}
