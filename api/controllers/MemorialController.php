<?php

namespace Controllers;

use Core\ImageService;
use Core\Input;
use Core\Request;
use Core\Response;
use Exception;
use Models\MemorialItem;

/**
 * Acervo Histórico do Memorial (objetos físicos catalogados).
 */
class MemorialController {
    private $items;

    public function __construct() {
        $this->items = new MemorialItem();
    }

    public function index() {
        [$items, $total] = $this->items->search([
            'q' => Request::query('q'),
            'categories' => Input::ids(Request::query('categories', '')),
            'sort' => Request::query('sort'),
        ], Request::page(), Request::perPage(12));

        Response::paginated($items, $total, Request::page(), Request::perPage(12));
    }

    public function show($id) {
        $item = $this->items->detail($id) ?: Response::error('Item do acervo não encontrado.', 404);
        Response::json($item);
    }

    public function store() {
        $data = $this->validated();
        $main = Request::file('main_image');
        if (!$main) {
            Response::error('Envie a foto principal do item.', 422, ['main_image' => 'Envie a foto principal do item.']);
        }
        $data['main_image_url'] = $this->process($main);

        $id = $this->items->create($data);
        $this->items->syncCategories($id, Input::ids(Request::input('category_ids', [])));
        $this->storeExtraImages($id);

        Response::json($this->items->detail($id), 201);
    }

    public function update($id) {
        $item = $this->items->find($id) ?: Response::error('Item do acervo não encontrado.', 404);
        $data = $this->validated();

        $main = Request::file('main_image');
        $data['main_image_url'] = $main ? $this->process($main) : $item['main_image_url'];

        $this->items->update($id, $data);
        if (Request::has('category_ids')) {
            $this->items->syncCategories($id, Input::ids(Request::input('category_ids', [])));
        }
        $this->storeExtraImages($id);

        Response::json($this->items->detail($id));
    }

    public function destroy($id) {
        $this->items->find($id) ?: Response::error('Item do acervo não encontrado.', 404);
        $this->items->softDelete($id);
        Response::json(['message' => 'Item removido do acervo.']);
    }

    public function destroyImage($id, $imageId) {
        if (!$this->items->removeImage($id, $imageId)) {
            Response::error('Imagem não encontrada.', 404);
        }
        Response::json(['message' => 'Imagem removida.']);
    }

    private function validated() {
        Input::require(['title' => 'Nome do item']);
        $description = Input::html(Request::input('historical_description', ''));
        if (!Input::htmlHasContent($description)) {
            Response::error('Escreva a descrição e o contexto histórico do item.', 422, ['historical_description' => 'Campo obrigatório.']);
        }
        return [
            'title' => mb_substr(Request::input('title'), 0, 255),
            'historical_description' => $description,
        ];
    }

    private function storeExtraImages($id) {
        foreach (Request::files('images') as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $this->items->addImage($id, $this->process($file));
            }
        }
    }

    private function process($file) {
        try {
            return (new ImageService())->processAndSave($file, 'memorial')['image_path'];
        } catch (Exception $e) {
            Response::error('Não foi possível processar a imagem "' . $file['name'] . '": ' . $e->getMessage(), 422);
        }
    }
}
