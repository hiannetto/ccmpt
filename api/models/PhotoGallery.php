<?php

namespace Models;

class PhotoGallery extends Model {
    protected $table = 'photo_galleries';
    protected $softDeletes = true;

    private $select = "g.id, g.title, g.slug, g.description, g.cover_photo_id, g.created_at, g.updated_at,
        (SELECT COUNT(*) FROM photos p WHERE p.gallery_id = g.id AND p.deleted_at IS NULL) AS photo_count,
        COALESCE(
            (SELECT p.thumbnail_path FROM photos p WHERE p.id = g.cover_photo_id AND p.deleted_at IS NULL LIMIT 1),
            (SELECT p.thumbnail_path FROM photos p WHERE p.gallery_id = g.id AND p.deleted_at IS NULL ORDER BY p.id LIMIT 1)
        ) AS cover_thumbnail";

    public function all($onlyWithPhotos) {
        $sql = "SELECT {$this->select} FROM photo_galleries g WHERE g.deleted_at IS NULL"
            . ($onlyWithPhotos ? ' HAVING photo_count > 0' : '')
            . ' ORDER BY g.created_at DESC';
        return array_map([self::class, 'format'], $this->query($sql)->fetchAll());
    }

    public function detail($idOrSlug) {
        $field = ctype_digit((string) $idOrSlug) ? 'g.id' : 'g.slug';
        $row = $this->query(
            "SELECT {$this->select} FROM photo_galleries g WHERE $field = ? AND g.deleted_at IS NULL LIMIT 1",
            [$idOrSlug]
        )->fetch();
        return $row ? self::format($row) : null;
    }

    public function findByTitle($title) {
        return $this->query('SELECT * FROM photo_galleries WHERE title = ? AND deleted_at IS NULL LIMIT 1', [$title])->fetch() ?: null;
    }

    public function create($title, $slug, $description) {
        $this->query('INSERT INTO photo_galleries (title, slug, description) VALUES (?, ?, ?)', [$title, $slug, $description]);
        return $this->lastId();
    }

    public function update($id, $title, $slug, $description, $coverPhotoId = null) {
        $this->query('UPDATE photo_galleries SET title = ?, slug = ?, description = ?, cover_photo_id = ? WHERE id = ?', [$title, $slug, $description, $coverPhotoId, $id]);
    }

    public static function format(array $g) {
        $g['id'] = (int) $g['id'];
        $g['photo_count'] = (int) $g['photo_count'];
        if (isset($g['cover_photo_id'])) {
            $g['cover_photo_id'] = $g['cover_photo_id'] ? (int) $g['cover_photo_id'] : null;
        }
        $g['cover_thumbnail_url'] = \Core\ImageService::thumbFor($g['cover_thumbnail'] ?? '');
        return $g;
    }
}
