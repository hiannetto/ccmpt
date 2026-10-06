<?php

namespace Controllers;

use Core\ImageService;
use Core\Input;
use Core\Request;
use Core\Response;
use Core\Slug;
use Exception;
use Models\Post;

/**
 * Notícias e Eventos (Administrador e Editor).
 */
class PostController {
    private $posts;

    public function __construct() {
        $this->posts = new Post();
    }

    /** Público: apenas publicados. Equipe logada: tudo. */
    public function index() {
        [$items, $total] = $this->posts->search([
            'type' => Request::query('type'),
            'q' => Request::query('q'),
            'when' => Request::query('when'),
            'published_only' => !Request::user() || Request::query('published') === '1',
        ], Request::page(), Request::perPage(9));

        Response::paginated($items, $total, Request::page(), Request::perPage(9));
    }

    public function show($id) {
        $post = $this->posts->find($id) ?: Response::error('Publicação não encontrada.', 404);
        Response::json(Post::format($post));
    }

    public function showBySlug($slug) {
        $post = $this->posts->findBySlug($slug);
        if (!$post || (!$post['is_published'] && !Request::user())) {
            Response::error('Publicação não encontrada.', 404);
        }
        Response::json(Post::format($post));
    }

    public function store() {
        $data = $this->validated();
        $data['slug'] = Slug::unique($data['title'], 'posts');
        $data['cover_image_url'] = $this->uploadCover(null);

        $id = $this->posts->create($data);
        Response::json(Post::format($this->posts->find($id)), 201);
    }

    public function update($id) {
        $post = $this->posts->find($id) ?: Response::error('Publicação não encontrada.', 404);
        $data = $this->validated();
        $data['slug'] = $post['title'] === $data['title'] ? $post['slug'] : Slug::unique($data['title'], 'posts', $id);

        $cover = $post['cover_image_url'];
        if (Input::bool(Request::input('remove_cover'), false)) {
            $cover = null;
        }
        $data['cover_image_url'] = $this->uploadCover($cover);

        $this->posts->update($id, $data);
        Response::json(Post::format($this->posts->find($id)));
    }

    public function destroy($id) {
        $this->posts->find($id) ?: Response::error('Publicação não encontrada.', 404);
        $this->posts->softDelete($id);
        Response::json(['message' => 'Publicação excluída.']);
    }

    private function validated() {
        Input::require(['title' => 'Título']);
        $type = Request::input('type', 'news');
        if (!in_array($type, ['news', 'event'], true)) {
            Response::error('Tipo inválido.', 422);
        }
        $content = Input::html(Request::input('content', ''));
        if (!Input::htmlHasContent($content)) {
            Response::error('Escreva o texto da publicação.', 422, ['content' => 'Escreva o texto da publicação.']);
        }
        $eventDate = Input::datetime(Request::input('event_date'));
        if ($type === 'event' && !$eventDate) {
            Response::error('Informe a data do evento.', 422, ['event_date' => 'Informe a data do evento.']);
        }
        return [
            'type' => $type,
            'title' => mb_substr(Request::input('title'), 0, 255),
            'content' => $content,
            'event_date' => $type === 'event' ? $eventDate : null,
            'is_published' => Input::bool(Request::input('is_published')),
        ];
    }

    private function uploadCover($current) {
        $file = Request::file('cover_image');
        if (!$file) {
            return $current;
        }
        try {
            return (new ImageService())->processAndSave($file, 'posts')['image_path'];
        } catch (Exception $e) {
            Response::error('Não foi possível processar a imagem de capa: ' . $e->getMessage(), 422);
        }
    }
}
