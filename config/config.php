<?php
// Iniciar sessão antes de qualquer output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Caminho para o .env
$dotenvPath = __DIR__ . '/..';

try {
    // Carregar variáveis de ambiente
    $dotenv = Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
} catch (Exception $e) {
    die("Erro ao carregar .env: " . $e->getMessage());
}

// Configurações globais com fallback
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'dgt-marketing');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'root');

// Outras configurações
define('SITE_NAME', $_ENV['SITE_NAME'] ?? 'DGT Marketing');
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? 'atendimento@dgtmarketing.com.br');
