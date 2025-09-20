<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

use DGTMarketing\Security\CSRFProtection;
use DGTMarketing\Security\SecurityLogger;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfProtection = new CSRFProtection();

    // Validação CSRF
    if (!$csrfProtection->validateToken($_POST['csrf_token'] ?? '')) {
        // Log de tentativa
        SecurityLogger::logCSRFAttempt([
            'ip' => $_SERVER['REMOTE_ADDR'],
            'endpoint' => 'contact_form',
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ]);

        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Sessão inválida. Recarregue o formulário.'
        ]);
        exit;
    }

    // Sanitização e validação
    $name = sanitizeInput($_POST['nome'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['telefone'] ?? '');
    $message = sanitizeInput($_POST['mensagem'] ?? '');

    // Validações
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Nome é obrigatório';
    }

    if (empty($email) || !validateEmail($email)) {
        $errors[] = 'E-mail inválido';
    }

    if (empty($phone) || !validatePhone($phone)) {
        $errors[] = 'Telefone inválido';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }

    try {
        // Salvar no banco
        $database = new Database();
        $conn = $database->getConnection();

        if ($conn) {
            $stmt = $conn->prepare(
                "INSERT INTO leads (name, email, phone, message, ip_address, user_agent) 
                 VALUES (:name, :email, :phone, :message, :ip_address, :user_agent)"
            );

            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':message' => $message,
                ':ip_address' => $_SERVER['REMOTE_ADDR'],
                ':user_agent' => $_SERVER['HTTP_USER_AGENT']
            ]);

            // Log de sucesso
            SecurityLogger::logSecurityEvent('contact_form_success', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso!'
            ]);
        } else {
            throw new Exception('Erro de conexão com banco de dados');
        }

    } catch (Exception $e) {
        // Log de erro
        SecurityLogger::logSecurityEvent('contact_form_error', [
            'error' => $e->getMessage(),
            'email' => $email ?? 'N/A'
        ]);

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro interno. Tente novamente.'
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode([
    'success' => false,
    'message' => 'Método não permitido'
]);
