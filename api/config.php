<?php
/**
 * Configuração da API.
 * Em produção, copie este arquivo, ajuste as credenciais e NUNCA versione senhas reais.
 */
return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: 'localhost',
        'name'     => getenv('DB_NAME') ?: 'ccmpt',
        'user'     => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    ],

    // Troque por uma string longa e aleatória em produção
    'jwt_secret' => getenv('JWT_SECRET') ?: 'ccmpt-dev-secret-troque-em-producao-9f8e7d6c5b4a',
    'jwt_ttl'    => 60 * 60 * 12, // 12 horas

    // Uploads: pasta física e prefixo público da URL
    'upload_dir' => dirname(__DIR__) . '/api/public/uploads',
    'upload_url' => '/uploads',
    'max_upload_mb' => 25,

    // Origens permitidas (CORS). Em dev o Vite usa proxy, então mesma origem.
    'cors_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],
];
