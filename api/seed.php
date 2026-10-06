<?php
$root = __DIR__;
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

use Core\Database;

try {
    $db = Database::connection();

    // 1. Admin inicial
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Administrador', 'admin@ccmpt.com.br', '$password', 'admin')");

    // 2. Categorias base para o Acervo
    $categories = [
        ['década de 1950', 'decada-de-1950', 'decada'],
        ['década de 1960', 'decada-de-1960', 'decada'],
        ['década de 1970', 'decada-de-1970', 'decada'],
        ['década de 1980', 'decada-de-1980', 'decada'],
        ['década de 1990', 'decada-de-1990', 'decada'],
        ['década de 2000', 'decada-de-2000', 'decada'],
        ['Liturgia', 'liturgia', 'categoria'],
        ['Objetos Pessoais', 'objetos-pessoais', 'categoria'],
        ['Documentos', 'documentos', 'categoria'],
        ['Vestuário', 'vestuario', 'tipo_objeto'],
        ['Livro', 'livro', 'tipo_objeto'],
        ['Mobiliário', 'mobiliario', 'tipo_objeto'],
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO categories (name, slug, type) VALUES (?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }

    // 3. Páginas Institucionais base
    $pages = [
        [
            'A Instituição',
            'instituicao',
            '<p>O Centro Cultural e Memorial Padre Tiago foi idealizado para preservar a memória...</p>',
            1, 1
        ],
        [
            'História do Padre Tiago',
            'historia',
            '<p>Nascido na Itália, Padre Tiago dedicou sua vida...</p>',
            1, 1
        ],
        [
            'Projetos Sociais',
            'projetos',
            '<p>Nossos projetos envolvem escolinha de futebol, aulas de música...</p>',
            1, 1
        ]
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO pages (title, slug, content, is_published, show_in_menu) VALUES (?, ?, ?, ?, ?)");
    foreach ($pages as $p) {
        $stmt->execute($p);
    }

    echo "Seed executado com sucesso!\n";
    echo "Login: admin@ccmpt.com.br\n";
    echo "Senha: admin123\n";

} catch (Exception $e) {
    echo "Erro ao rodar o seed: " . $e->getMessage() . "\n";
}
