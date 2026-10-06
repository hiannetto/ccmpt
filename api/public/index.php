<?php

/**
 * Ponto de entrada único da API.
 *
 * Desenvolvimento:  php -d extension=gd -S localhost:8000 -t api/public api/public/index.php
 * Produção (Apache): ver .htaccess na raiz do site publicado.
 */

// No servidor embutido do PHP, arquivos existentes (ex.: /uploads/...) são servidos diretamente
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (is_file($file)) {
        return false;
    }
}

$root = dirname(__DIR__);
if (file_exists($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
}
spl_autoload_register(function ($class) use ($root) {
    $map = ['Core\\' => 'core/', 'Controllers\\' => 'controllers/', 'Models\\' => 'models/'];
    foreach ($map as $prefix => $dir) {
        if (strpos($class, $prefix) === 0) {
            $file = $root . '/' . $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    }
});

use Core\Config;
use Core\Response;
use Core\Router;

// CORS (em dev o Vite usa proxy; isto cobre acessos diretos de origens autorizadas)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && in_array($origin, Config::get('cors_origins', []), true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-HTTP-Method-Override');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Qualquer erro inesperado vira JSON (nunca HTML no meio da resposta)
set_exception_handler(function ($e) {
    error_log($e);
    Response::error('Erro interno no servidor.', 500);
});

$auth = ['Core\AuthMiddleware'];
$admin = ['Core\AuthMiddleware', 'Core\AdminMiddleware'];

$router = new Router();

// ---------- Autenticação ----------
$router->add('POST', '/api/login', 'AuthController@login');
$router->add('GET',  '/api/me', 'AuthController@me', $auth);
$router->add('PUT',  '/api/me', 'AuthController@updateMe', $auth);
$router->add('GET',  '/api/ping', 'AuthController@ping');

// ---------- Painel ----------
$router->add('GET', '/api/stats', 'StatsController@index', $auth);
$router->add('POST', '/api/uploads/image', 'UploadController@image', $auth);

// ---------- Equipe (Admin) ----------
$router->add('GET',    '/api/users', 'UserController@index', $admin);
$router->add('POST',   '/api/users', 'UserController@store', $admin);
$router->add('PUT',    '/api/users/:id', 'UserController@update', $admin);
$router->add('DELETE', '/api/users/:id', 'UserController@destroy', $admin);

// ---------- Notícias e Eventos (Admin + Editor) ----------
$router->add('GET',    '/api/posts', 'PostController@index');
$router->add('GET',    '/api/posts/slug/:slug', 'PostController@showBySlug');
$router->add('GET',    '/api/posts/:id', 'PostController@show', $auth);
$router->add('POST',   '/api/posts', 'PostController@store', $auth);
$router->add('PUT',    '/api/posts/:id', 'PostController@update', $auth);
$router->add('DELETE', '/api/posts/:id', 'PostController@destroy', $auth);

// ---------- Páginas estáticas (Admin) ----------
$router->add('GET',    '/api/pages', 'PageController@index');
$router->add('GET',    '/api/pages/slug/:slug', 'PageController@showBySlug');
$router->add('GET',    '/api/pages/:id', 'PageController@show', $admin);
$router->add('POST',   '/api/pages', 'PageController@store', $admin);
$router->add('PUT',    '/api/pages/:id', 'PageController@update', $admin);
$router->add('DELETE', '/api/pages/:id', 'PageController@destroy', $admin);

// ---------- Acervo e Classificações (Admin) ----------
$router->add('GET',    '/api/categories', 'CategoryController@index');
$router->add('POST',   '/api/categories', 'CategoryController@store', $admin);
$router->add('PUT',    '/api/categories/:id', 'CategoryController@update', $admin);
$router->add('DELETE', '/api/categories/:id', 'CategoryController@destroy', $admin);

$router->add('GET',    '/api/memorial', 'MemorialController@index');
$router->add('GET',    '/api/memorial/:id', 'MemorialController@show');
$router->add('POST',   '/api/memorial', 'MemorialController@store', $admin);
$router->add('PUT',    '/api/memorial/:id', 'MemorialController@update', $admin);
$router->add('DELETE', '/api/memorial/:id', 'MemorialController@destroy', $admin);
$router->add('DELETE', '/api/memorial/:id/images/:imageId', 'MemorialController@destroyImage', $admin);

// ---------- Arquivo Fotográfico (Admin + Editor; excluir álbum: Admin) ----------
$router->add('GET',    '/api/galleries', 'GalleryController@index');
$router->add('GET',    '/api/galleries/:id', 'GalleryController@show');
$router->add('GET',    '/api/galleries/:id/photos', 'GalleryController@photos');
$router->add('POST',   '/api/galleries', 'GalleryController@store', $auth);
$router->add('PUT',    '/api/galleries/:id', 'GalleryController@update', $auth);
$router->add('DELETE', '/api/galleries/:id', 'GalleryController@destroy', $admin);
$router->add('POST',   '/api/galleries/:id/photos', 'GalleryController@upload', $auth);
$router->add('PUT',    '/api/photos/:id', 'PhotoController@update', $auth);
$router->add('DELETE', '/api/photos/:id', 'PhotoController@destroy', $auth);

// ---------- Contatos ----------
$router->add('POST', '/api/contacts', 'ContactController@store'); // público (formulário do site)
$router->add('GET',  '/api/contacts', 'ContactController@index', $admin);
$router->add('GET',  '/api/contacts/:id', 'ContactController@show', $admin);
$router->add('PUT',  '/api/contacts/:id/status', 'ContactController@updateStatus', $admin);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
