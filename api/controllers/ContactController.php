<?php

namespace Controllers;

use Core\Request;
use Core\Response;
use Models\Contact;

/**
 * Central de Atendimento: mensagens do formulário de contato.
 */
class ContactController {
    private $contacts;

    public function __construct() {
        $this->contacts = new Contact();
    }

    public function index() {
        $perPage = Request::perPage(20);
        [$items, $total] = $this->contacts->search(Request::query('status'), Request::page(), $perPage);
        Response::json([
            'data' => $items,
            'counts' => $this->contacts->counts(),
            'meta' => [
                'page' => Request::page(),
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    public function show($id) {
        $contact = $this->contacts->find($id) ?: Response::error('Mensagem não encontrada.', 404);
        Response::json(Contact::format($contact));
    }

    /** Público: recebe a mensagem do site. */
    public function store() {
        // Campo "armadilha" invisível para humanos: robôs costumam preenchê-lo
        if (Request::input('website')) {
            Response::json(['message' => 'Mensagem enviada.'], 201);
        }

        $name = mb_substr((string) Request::input('name'), 0, 255);
        $email = mb_strtolower(mb_substr((string) Request::input('email'), 0, 255));
        $message = mb_substr((string) Request::input('message'), 0, 5000);
        $errors = [];

        if (mb_strlen($name) < 2) {
            $errors['name'] = 'Informe seu nome.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail válido.';
        }
        if (mb_strlen($message) < 10) {
            $errors['message'] = 'Escreva sua mensagem (mínimo de 10 caracteres).';
        }
        if ($errors) {
            Response::error(reset($errors), 422, $errors);
        }

        $id = $this->contacts->create([
            'name' => strip_tags($name),
            'email' => $email,
            'phone' => mb_substr(preg_replace('/[^0-9()+\-\s]/', '', (string) Request::input('phone')), 0, 30) ?: null,
            'subject' => mb_substr(strip_tags((string) Request::input('subject')), 0, 150) ?: null,
            'message' => strip_tags($message),
        ]);

        Response::json(['message' => 'Mensagem enviada com sucesso.', 'id' => $id], 201);
    }

    public function updateStatus($id) {
        $status = Request::input('status');
        if (!in_array($status, ['pendente', 'atendido'], true)) {
            Response::error('Status inválido.', 422);
        }
        $this->contacts->find($id) ?: Response::error('Mensagem não encontrada.', 404);
        $this->contacts->updateStatus($id, $status);
        Response::json(Contact::format($this->contacts->find($id)));
    }
}
