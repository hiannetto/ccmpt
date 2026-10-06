<?php

namespace Controllers;

use Core\Request;
use Core\Response;
use Models\Photo;

class PhotoController {
    private $photos;

    public function __construct() {
        $this->photos = new Photo();
    }

    public function update($id) {
        $this->photos->find($id) ?: Response::error('Foto não encontrada.', 404);
        $this->photos->updateTags($id, mb_substr((string) Request::input('tags', ''), 0, 255) ?: null);
        Response::json(Photo::format($this->photos->find($id)));
    }

    /** Exclusão lógica: o arquivo físico é preservado. */
    public function destroy($id) {
        $this->photos->find($id) ?: Response::error('Foto não encontrada.', 404);
        $this->photos->softDelete($id);
        Response::json(['message' => 'Foto removida.']);
    }
}
