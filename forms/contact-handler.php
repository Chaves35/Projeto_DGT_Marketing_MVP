<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configurações de segurança
header('Content-Type: application/json');

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Remove caracteres não numéricos
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^[1-9]{2}9?[0-9]{8}$/', $phone);
}

function sendEmail($name, $email, $phone, $message) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Remetente e destinatário
        $mail->setFrom($email, $name);
        $mail->addAddress(ADMIN_EMAIL, SITE_NAME);
        $mail->addReplyTo($email, $name);

        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'Novo Lead - ' . SITE_NAME;
        $mail->Body    = "
        <h2>Novo Lead Recebido</h2>
        <p><strong>Nome:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Telefone:</strong> {$phone}</p>
        <p><strong>Mensagem:</strong> {$message}</p>
        <p>Recebido em: " . date('d/m/Y H:i:s') . "</p>
        ";
        $mail->AltBody = "Nome: {$name}\nEmail: {$email}\nTelefone: {$phone}\nMensagem: {$message}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log de erro
        error_log("Erro ao enviar email: " . $mail->ErrorInfo);
        return false;
    }
}

function saveLeadToDatabase($data) {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO leads (
                name, email, phone, message, 
                ip_address, user_agent, utm_source, 
                utm_medium, utm_campaign
            ) VALUES (
                :name, :email, :phone, :message, 
                :ip_address, :user_agent, :utm_source, 
                :utm_medium, :utm_campaign
            )"
        );

        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':message' => $data['message'],
            ':ip_address' => $_SERVER['REMOTE_ADDR'],
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
            ':utm_source' => $_GET['utm_source'] ?? 'direto',
            ':utm_medium' => $_GET['utm_medium'] ?? 'website',
            ':utm_campaign' => $_GET['utm_campaign'] ?? 'geral'
        ]);

        return true;
    } catch (PDOException $e) {
        error_log("Erro ao salvar lead: " . $e->getMessage());
        return false;
    }
}

// Processamento da requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura e sanitiza dados
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
        $errors[] = 'Email inválido';
    }

    if (empty($phone) || !validatePhone($phone)) {
        $errors[] = 'Telefone inválido';
    }

    // Resposta em caso de erros
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }

    // Processamento
    $emailSent = sendEmail($name, $email, $phone, $message);
    $leadSaved = saveLeadToDatabase([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message
    ]);

    // Resposta
    if ($emailSent && $leadSaved) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Seu contato foi recebido! Em breve retornaremos.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar seu contato. Tente novamente.'
        ]);
    }
    exit;
}
