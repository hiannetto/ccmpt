<?php

namespace Controllers;

use Core\Input;
use Core\Request;
use Core\Response;
use Core\Slug;
use Models\Category;

class CategoryController {
    private $categories;

    public function __construct() {
        $this->categories = new Category();
    }

    public function index() {
        Response::json([
            'types' => Category::TYPES,
            'data' => $this->categories->all(true),
        ]);
    }

    public function store() {
        [$name, $type] = $this->validated();
        $id = $this->categories->create($name, Slug::unique($type . '-' . $name, 'categories'), $type);
        Response::json(['id' => $id, 'name' => $name, 'type' => $type], 201);
    }

    public function update($id) {
        $this->categories->find($id) ?: Response::error('Classificação não encontrada.', 404);
        [$name, $type] = $this->validated();
        $this->categories->update($id, $name, Slug::unique($type . '-' . $name, 'categories', $id), $type);
        Response::json(['id' => (int) $id, 'name' => $name, 'type' => $type]);
    }

    public function destroy($id) {
        $this->categories->find($id) ?: Response::error('Classificação não encontrada.', 404);
        $this->categories->delete($id);
        Response::json(['message' => 'Classificação excluída.']);
    }

    private function validated() {
        Input::require(['name' => 'Nome', 'type' => 'Tipo']);
        $type = Request::input('type');
        if (!array_key_exists($type, Category::TYPES)) {
            Response::error('Tipo de classificação inválido.', 422);
        }
        return [mb_substr(Request::input('name'), 0, 100), $type];
    }
}
