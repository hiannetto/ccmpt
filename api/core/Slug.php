<?php

namespace Core;

/**
 * Geração de endereços amigáveis (slugs) únicos.
 */
class Slug {
    public static function make($text) {
        $text = mb_strtolower(trim((string) $text), 'UTF-8');
        $map = [
            'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c','ñ'=>'n',
        ];
        $text = strtr($text, $map);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text !== '' ? substr($text, 0, 180) : 'item';
    }

    /** Garante unicidade na tabela, acrescentando -2, -3... */
    public static function unique($text, $table, $ignoreId = null) {
        $base = self::make($text);
        $slug = $base;
        $i = 2;
        $db = Database::connection();
        while (true) {
            $sql = "SELECT id FROM `$table` WHERE slug = ?" . ($ignoreId ? ' AND id <> ?' : '') . ' LIMIT 1';
            $stmt = $db->prepare($sql);
            $stmt->execute($ignoreId ? [$slug, $ignoreId] : [$slug]);
            if (!$stmt->fetch()) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }
}
