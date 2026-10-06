<?php

namespace Models;

class Photo extends Model {
    protected $table = 'photos';
    protected $softDeletes = true;

    public function byGallery($galleryId, $page, $perPage) {
        [$items, $total] = $this->paginate(
            'id, gallery_id, image_path, thumbnail_path, tags, created_at',
            'photos', ['gallery_id = ?', 'deleted_at IS NULL'], [$galleryId], 'id ASC', $page, $perPage
        );
        return [array_map([self::class, 'format'], $items), $total];
    }

    public function create($galleryId, $imagePath, $thumbnailPath, $tags = null) {
        $this->query(
            'INSERT INTO photos (gallery_id, image_path, thumbnail_path, tags) VALUES (?, ?, ?, ?)',
            [$galleryId, $imagePath, $thumbnailPath, $tags]
        );
        return $this->lastId();
    }

    public function updateTags($id, $tags) {
        $this->query('UPDATE photos SET tags = ? WHERE id = ?', [$tags, $id]);
    }

    public static function format(array $p) {
        $p['id'] = (int) $p['id'];
        $p['gallery_id'] = (int) $p['gallery_id'];
        return $p;
    }
}
