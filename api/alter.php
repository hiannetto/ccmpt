<?php
$root = __DIR__;
require $root . '/core/Config.php';
require $root . '/core/Database.php';

use Core\Database;

try {
    $db = Database::connection();

    // Adiciona as colunas ausentes
    $db->exec("ALTER TABLE pages ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 1 AFTER content");
    $db->exec("ALTER TABLE pages ADD COLUMN show_in_menu TINYINT(1) NOT NULL DEFAULT 1 AFTER is_published");

    $db->exec("ALTER TABLE posts ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 1 AFTER event_date");

    $db->exec("ALTER TABLE contacts ADD COLUMN phone VARCHAR(30) NULL AFTER email");
    $db->exec("ALTER TABLE contacts ADD COLUMN subject VARCHAR(150) NULL AFTER phone");

    echo "Tabelas alteradas com sucesso!\n";
} catch (Exception $e) {
    echo "Erro ao alterar tabelas: " . $e->getMessage() . "\n";
}
