<?php
/**
 * Migrações do banco de dados.
 *
 * Pode ser executado quantas vezes for necessário: cada alteração usa
 * "IF NOT EXISTS" (MariaDB 10.0+) ou é naturalmente idempotente (MODIFY).
 *
 * Uso: php api/alter.php
 */
$root = __DIR__;
require $root . '/core/Config.php';
require $root . '/core/Database.php';

use Core\Database;

$migrations = [
    // Adiciona as colunas ausentes
    'pages.is_published' => "ALTER TABLE pages ADD COLUMN IF NOT EXISTS is_published TINYINT(1) NOT NULL DEFAULT 1 AFTER content",
    'pages.show_in_menu' => "ALTER TABLE pages ADD COLUMN IF NOT EXISTS show_in_menu TINYINT(1) NOT NULL DEFAULT 1 AFTER is_published",
    'posts.is_published' => "ALTER TABLE posts ADD COLUMN IF NOT EXISTS is_published TINYINT(1) NOT NULL DEFAULT 1 AFTER event_date",
    'contacts.phone'     => "ALTER TABLE contacts ADD COLUMN IF NOT EXISTS phone VARCHAR(30) NULL AFTER email",
    'contacts.subject'   => "ALTER TABLE contacts ADD COLUMN IF NOT EXISTS subject VARCHAR(150) NULL AFTER phone",

    // Textos longos: TEXT limita a 64 KB, MEDIUMTEXT comporta até 16 MB
    'pages.content (MEDIUMTEXT)' => "ALTER TABLE pages MODIFY content MEDIUMTEXT NOT NULL",
    'posts.content (MEDIUMTEXT)' => "ALTER TABLE posts MODIFY content MEDIUMTEXT NOT NULL",

    // ---------- Acervo Histórico: datação e ficha técnica (todos opcionais) ----------
    'memorial_items.historical_description (MEDIUMTEXT)' =>
        "ALTER TABLE memorial_items MODIFY historical_description MEDIUMTEXT NOT NULL",
    'memorial_items.main_image_url (comentário)' =>
        "ALTER TABLE memorial_items MODIFY main_image_url VARCHAR(500) NOT NULL COMMENT 'Caminho local da foto principal em WebP'",
    'memorial_items.main_image_caption' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS main_image_caption VARCHAR(255) NULL AFTER main_image_url",
    'memorial_items.dating_label' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS dating_label VARCHAR(100) NULL COMMENT 'Datação em texto livre (ex.: c. 1965, década de 1970)' AFTER title",
    'memorial_items.year' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS year SMALLINT NULL COMMENT 'Ano de referência para ordenação e sugestão de década' AFTER dating_label",
    'memorial_items.inventory_number' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS inventory_number VARCHAR(50) NULL COMMENT 'Nº de inventário / tombo' AFTER year",
    'memorial_items.material' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS material VARCHAR(255) NULL AFTER historical_description",
    'memorial_items.dimensions' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS dimensions VARCHAR(255) NULL AFTER material",
    'memorial_items.provenance' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS provenance VARCHAR(255) NULL COMMENT 'Procedência' AFTER dimensions",
    'memorial_items.conservation_state' =>
        "ALTER TABLE memorial_items ADD COLUMN IF NOT EXISTS conservation_state VARCHAR(20) NULL COMMENT 'otimo, bom, regular, ruim' AFTER provenance",

    // ---------- Fotos extras das peças: legenda e exclusão lógica ----------
    'memorial_images.caption' =>
        "ALTER TABLE memorial_images ADD COLUMN IF NOT EXISTS caption VARCHAR(255) NULL AFTER image_url",
    'memorial_images.deleted_at' =>
        "ALTER TABLE memorial_images ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL AFTER created_at",

    // ---------- Arquivo Fotográfico: legenda da foto e capa do álbum ----------
    'photos.caption' =>
        "ALTER TABLE photos ADD COLUMN IF NOT EXISTS caption VARCHAR(255) NULL AFTER thumbnail_path",
    'photo_galleries.cover_photo_id' =>
        "ALTER TABLE photo_galleries ADD COLUMN IF NOT EXISTS cover_photo_id INT(11) NULL COMMENT 'Foto de capa escolhida (padrão: a primeira)' AFTER description",
];

try {
    $db = Database::connection();
} catch (Exception $e) {
    echo "Erro ao conectar: " . $e->getMessage() . "\n";
    exit(1);
}

$failures = 0;
foreach ($migrations as $label => $sql) {
    try {
        $db->exec($sql);
        echo "[ok]   $label\n";
    } catch (Exception $e) {
        $failures++;
        echo "[erro] $label: " . $e->getMessage() . "\n";
    }
}

echo $failures ? "\nConcluído com $failures erro(s).\n" : "\nTabelas alteradas com sucesso!\n";
exit($failures ? 1 : 0);
