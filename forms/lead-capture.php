<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^[1-9]{2}9?[0-9]{8}$/', $phone);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['nome'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['telefone'] ?? '');
    $company = sanitizeInput($_POST['empresa'] ?? '');
    $message = sanitizeInput($_POST['mensagem'] ?? '');

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Nome é obrigatório';
    }

    if (empty($email) || !validateEmail($email)) {
        $errors[] = 'Email inválido';
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
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO leads (
                name, email, phone, company, message, 
                ip_address, user_agent, 
                utm_source, utm_medium, utm_campaign
            ) VALUES (
                :name, :email, :phone, :company, :message, 
                :ip_address, :user_agent, 
                :utm_source, :utm_medium, :utm_campaign
            )"
        );

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':company' => $company,
            ':message' => $message,
            ':ip_address' => $_SERVER['REMOTE_ADDR'],
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
            ':utm_source' => $_GET['utm_source'] ?? 'direto',
            ':utm_medium' => $_GET['utm_medium'] ?? 'website',
            ':utm_campaign' => $_GET['utm_campaign'] ?? 'geral'
        ]);

        // Envio de email de notificação
        $to = ADMIN_EMAIL;
        $subject = "Novo Lead - " . SITE_NAME;
        $emailBody = "
        Novo lead capturado:\n
        Nome: $name\n
        Email: $email\n
        Telefone: $phone\n
        Empresa: $company\n
        Mensagem: $message\n
        IP: " . $_SERVER['REMOTE_ADDR'] . "
        ";
        
        $headers = "From: " . SMTP_USERNAME;
        
        mail($to, $subject, $emailBody, $headers);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Lead capturado com sucesso! Entraremos em contato.'
        ]);
    } catch (PDOException $e) {
        error_log("Erro de banco de dados: " . $e->getMessage());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro interno. Tente novamente mais tarde.'
        ]);
    }
    exit;
}
