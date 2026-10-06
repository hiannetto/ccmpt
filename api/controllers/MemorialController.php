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
        
        // Verifica número de inventário duplicado
        if (!empty($data['inventory_number']) && $this->items->findByInventory($data['inventory_number'])) {
            Response::error('Este número de inventário já está em uso.', 422, ['inventory_number' => 'Número já em uso.']);
        }

        $main = Request::file('main_image');
        if (!$main) {
            Response::error('Envie a foto principal do item.', 422, ['main_image' => 'Envie a foto principal do item.']);
        }
        $data['main_image_url'] = $this->process($main);

        $id = $this->items->create($data);
        $this->items->syncCategories($id, Input::ids(Request::input('category_ids', [])));

        Response::json($this->items->detail($id), 201);
    }

    public function update($id) {
        $item = $this->items->find($id) ?: Response::error('Item do acervo não encontrado.', 404);
        $data = $this->validated();

        if (!empty($data['inventory_number']) && $this->items->findByInventory($data['inventory_number'], $id)) {
            Response::error('Este número de inventário já está em uso.', 422, ['inventory_number' => 'Número já em uso.']);
        }

        $main = Request::file('main_image');
        $data['main_image_url'] = $main ? $this->process($main) : $item['main_image_url'];

        $this->items->update($id, $data);
        if (Request::has('category_ids')) {
            $this->items->syncCategories($id, Input::ids(Request::input('category_ids', [])));
        }

        Response::json($this->items->detail($id));
    }

    public function destroy($id) {
        $this->items->find($id) ?: Response::error('Item do acervo não encontrado.', 404);
        $this->items->softDelete($id);
        Response::json(['message' => 'Item removido do acervo.']);
    }

    // ---------- Gestão de Imagens ----------

    public function uploadImage($id) {
        $this->items->find($id) ?: Response::error('Item do acervo não encontrado.', 404);
        $file = Request::file('image');
        if (!$file) {
            Response::error('Envie uma imagem válida.', 422, ['image' => 'Arquivo ausente.']);
        }
        $url = $this->process($file);
        $caption = Request::input('caption', null);
        $imageId = $this->items->addImage($id, $url, $caption);
        
        Response::json([
            'message' => 'Imagem adicionada.',
            'image' => MemorialItem::formatImage([
                'id' => $imageId,
                'image_url' => $url,
                'caption' => $caption
            ])
        ], 201);
    }

    public function updateImage($id, $imageId) {
        $this->items->find($id) ?: Response::error('Item do acervo não encontrado.', 404);
        $caption = Request::input('caption');
        
        if ($imageId === 'main') {
            $this->items->updateMainCaption($id, $caption);
        } else {
            $this->items->findImage($id, $imageId) ?: Response::error('Imagem não encontrada.', 404);
            $this->items->updateImageCaption($id, $imageId, $caption);
        }
        
        Response::json(['message' => 'Legenda atualizada.']);
    }

    public function setMainImage($id, $imageId) {
        if (!$this->items->setMainImage($id, $imageId)) {
            Response::error('Item ou imagem não encontrados.', 404);
        }
        Response::json(['message' => 'Foto principal atualizada.']);
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
            'dating_label' => Request::input('dating_label') ? mb_substr(Request::input('dating_label'), 0, 100) : null,
            'year' => Request::input('year') ? (int) Request::input('year') : null,
            'inventory_number' => Request::input('inventory_number') ? mb_substr(Request::input('inventory_number'), 0, 50) : null,
            'material' => Request::input('material') ? mb_substr(Request::input('material'), 0, 150) : null,
            'dimensions' => Request::input('dimensions') ? mb_substr(Request::input('dimensions'), 0, 150) : null,
            'provenance' => Request::input('provenance') ? mb_substr(Request::input('provenance'), 0, 255) : null,
            'conservation_state' => Request::input('conservation_state') && array_key_exists(Request::input('conservation_state'), MemorialItem::CONSERVATION) ? Request::input('conservation_state') : null,
            'main_image_caption' => Request::input('main_image_caption') ? mb_substr(Request::input('main_image_caption'), 0, 255) : null,
        ];
    }

    private function process($file) {
        try {
            return (new ImageService())->processAndSave($file, 'memorial')['image_path'];
        } catch (Exception $e) {
            Response::error('Não foi possível processar a imagem "' . $file['name'] . '": ' . $e->getMessage(), 422);
        }
    }
}
