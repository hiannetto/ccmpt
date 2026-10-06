<?php

namespace Controllers;

use Core\ImageService;
use Core\Request;
use Core\Response;
use Core\Slug;
use Exception;
use Models\Photo;
use Models\PhotoGallery;

/**
 * Arquivo Fotográfico: álbuns e envio de fotos em lote.
 */
class GalleryController {
    private $galleries;
    private $photos;

    public function __construct() {
        $this->galleries = new PhotoGallery();
        $this->photos = new Photo();
    }

    /** Público: só álbuns com fotos. Equipe logada: todos. */
    public function index() {
        Response::json($this->galleries->all(!Request::user()));
    }

    public function show($idOrSlug) {
        $gallery = $this->galleries->detail($idOrSlug) ?: Response::error('Álbum não encontrado.', 404);
        Response::json($gallery);
    }

    public function photos($idOrSlug) {
        $gallery = $this->galleries->detail($idOrSlug) ?: Response::error('Álbum não encontrado.', 404);
        $perPage = Request::perPage(30, 100);
        [$items, $total] = $this->photos->byGallery($gallery['id'], Request::page(), $perPage);
        Response::paginated($items, $total, Request::page(), $perPage);
    }

    /** Cria um álbum (usado pela tela de envio, sem sair da página). */
    public function store() {
        $title = trim((string) Request::input('title'));
        if ($title === '') {
            Response::error('Informe o nome do álbum.', 422, ['title' => 'Informe o nome do álbum.']);
        }
        if ($this->galleries->findByTitle($title)) {
            Response::error('Já existe um álbum com este nome.', 422, ['title' => 'Álbum já existe.']);
        }
        $id = $this->galleries->create(mb_substr($title, 0, 255), Slug::unique($title, 'photo_galleries'), Request::input('description') ?: null);
        Response::json($this->galleries->detail($id), 201);
    }

    public function update($id) {
        $gallery = $this->galleries->detail($id) ?: Response::error('Álbum não encontrado.', 404);
        $title = trim((string) Request::input('title', $gallery['title']));
        if ($title === '') {
            Response::error('Informe o nome do álbum.', 422);
        }
        $slug = $title === $gallery['title'] ? $gallery['slug'] : Slug::unique($title, 'photo_galleries', $id);
        
        $coverPhotoId = Request::has('cover_photo_id') ? Request::input('cover_photo_id') : $gallery['cover_photo_id'];

        $this->galleries->update($id, $title, $slug, Request::input('description', $gallery['description']) ?: null, $coverPhotoId);
        Response::json($this->galleries->detail($id));
    }

    public function destroy($id) {
        $this->galleries->find($id) ?: Response::error('Álbum não encontrado.', 404);
        $this->galleries->softDelete($id);
        Response::json(['message' => 'Álbum excluído.']);
    }

    /**
     * Recebe um lote de fotos (photos[]) para o álbum.
     * O painel envia em pequenos lotes para não estourar limites do servidor.
     */
    public function upload($id) {
        $gallery = $this->galleries->find($id) ?: Response::error('Álbum não encontrado.', 404);
        $files = Request::files('photos');
        if (!$files) {
            Response::error('Nenhuma foto foi recebida. Verifique o tamanho dos arquivos.', 422);
        }

        try {
            $service = new ImageService();
        } catch (Exception $e) {
            Response::error($e->getMessage(), 422);
        }

        $uploaded = [];
        $errors = [];

        foreach ($files as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = ['file' => $file['name'], 'error' => $this->uploadErrorMessage($file['error'])];
                continue;
            }
            try {
                $paths = $service->processAndSave($file, 'galleries');
                $photoId = $this->photos->create($gallery['id'], $paths['image_path'], $paths['thumbnail_path']);
                $uploaded[] = ['id' => $photoId] + $paths;
            } catch (Exception $e) {
                $errors[] = ['file' => $file['name'], 'error' => $e->getMessage()];
            }
        }

        Response::json([
            'gallery_id' => (int) $gallery['id'],
            'success_count' => count($uploaded),
            'photos' => $uploaded,
            'errors' => $errors,
        ], $uploaded ? 201 : 422);
    }

    private function uploadErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE: return 'Arquivo maior que o limite do servidor.';
            case UPLOAD_ERR_PARTIAL: return 'Envio interrompido.';
            default: return 'Falha no envio do arquivo.';
        }
    }
}
