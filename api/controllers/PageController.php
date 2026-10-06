<?php

namespace Controllers;

use Core\Input;
use Core\Request;
use Core\Response;
use Core\Slug;
use Models\Page;

/**
 * Páginas estáticas institucionais (somente Administrador para escrita).
 */
class PageController {
    private $pages;

    public function __construct() {
        $this->pages = new Page();
    }

    public function index() {
        Response::json($this->pages->all(!Request::user()));
    }

    public function show($id) {
        $page = $this->pages->find($id) ?: Response::error('Página não encontrada.', 404);
        Response::json(Page::format($page));
    }

    public function showBySlug($slug) {
        $page = $this->pages->findBySlug($slug);
        if (!$page || (!$page['is_published'] && !Request::user())) {
            Response::error('Página não encontrada.', 404);
        }
        Response::json(Page::format($page));
    }

    public function store() {
        $data = $this->validated();
        $data['slug'] = $this->resolveSlug(Request::input('slug') ?: $data['title']);
        $id = $this->pages->create($data);
        Response::json(Page::format($this->pages->find($id)), 201);
    }

    public function update($id) {
        $page = $this->pages->find($id) ?: Response::error('Página não encontrada.', 404);
        $data = $this->validated();
        $requested = Request::input('slug') ?: $page['slug'];
        $data['slug'] = Slug::make($requested) === $page['slug'] ? $page['slug'] : $this->resolveSlug($requested, $id);
        $this->pages->update($id, $data);
        Response::json(Page::format($this->pages->find($id)));
    }

    public function destroy($id) {
        $this->pages->find($id) ?: Response::error('Página não encontrada.', 404);
        $this->pages->delete($id);
        Response::json(['message' => 'Página excluída.']);
    }

    private function resolveSlug($text, $ignoreId = null) {
        if (in_array(Slug::make($text), Page::RESERVED_SLUGS, true)) {
            Response::error('Este endereço já é usado pelo site. Escolha outro.', 422, ['slug' => 'Endereço reservado.']);
        }
        return Slug::unique($text, 'pages', $ignoreId);
    }

    private function validated() {
        Input::require(['title' => 'Título']);
        $content = Input::html(Request::input('content', ''));
        if (!Input::htmlHasContent($content)) {
            Response::error('Escreva o conteúdo da página.', 422, ['content' => 'Escreva o conteúdo da página.']);
        }
        return [
            'title' => mb_substr(Request::input('title'), 0, 255),
            'content' => $content,
            'is_published' => Input::bool(Request::input('is_published')),
            'show_in_menu' => Input::bool(Request::input('show_in_menu')),
        ];
    }
}
