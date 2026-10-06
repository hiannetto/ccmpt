<?php

namespace Controllers;

use Core\ImageService;
use Core\Request;
use Core\Response;
use Exception;

/**
 * Upload avulso de imagens inseridas no meio dos textos (editor visual).
 */
class UploadController {
    public function image() {
        $file = Request::file('image') ?: Response::error('Nenhuma imagem recebida.', 422);
        try {
            $paths = (new ImageService())->processAndSave($file, 'content');
        } catch (Exception $e) {
            Response::error('Não foi possível processar a imagem: ' . $e->getMessage(), 422);
        }
        Response::json(['url' => $paths['image_path'], 'thumbnail_url' => $paths['thumbnail_path']], 201);
    }
}
