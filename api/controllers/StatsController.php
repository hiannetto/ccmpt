<?php

namespace Controllers;

use Core\Database;
use Core\Request;
use Core\Response;

/**
 * Números do painel inicial do backoffice.
 */
class StatsController {
    public function index() {
        $db = Database::connection();
        $count = function ($sql) use ($db) {
            return (int) $db->query($sql)->fetchColumn();
        };

        $stats = [
            'news' => $count("SELECT COUNT(*) FROM posts WHERE type = 'news' AND deleted_at IS NULL"),
            'events_upcoming' => $count("SELECT COUNT(*) FROM posts WHERE type = 'event' AND event_date >= CURRENT_DATE AND deleted_at IS NULL"),
            'galleries' => $count('SELECT COUNT(*) FROM photo_galleries WHERE deleted_at IS NULL'),
            'photos' => $count('SELECT COUNT(*) FROM photos p JOIN photo_galleries g ON g.id = p.gallery_id AND g.deleted_at IS NULL WHERE p.deleted_at IS NULL'),
        ];

        if ((Request::user()['role'] ?? '') === 'admin') {
            $stats['memorial_items'] = $count('SELECT COUNT(*) FROM memorial_items WHERE deleted_at IS NULL');
            $stats['pages'] = $count('SELECT COUNT(*) FROM pages');
            
            // Abas de contatos (novas métricas)
            $stats['contacts_last_7_days'] = $count("SELECT COUNT(*) FROM contacts WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)");
            $stats['contacts_unread'] = $count("SELECT COUNT(*) FROM contacts WHERE status = 'pendente'");
            $stats['contacts_replied'] = $count("SELECT COUNT(*) FROM contacts WHERE status = 'atendido'");
            
            // Retrocompatibilidade
            $stats['contacts_pending'] = $stats['contacts_unread'];
            $stats['latest_contacts'] = $db->query("SELECT id, name, subject, created_at FROM contacts WHERE status = 'pendente' ORDER BY created_at DESC LIMIT 5")->fetchAll();
        }

        $stats['latest_posts'] = $db->query('SELECT id, type, title, is_published, created_at FROM posts WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5')->fetchAll();

        Response::json($stats);
    }
}
