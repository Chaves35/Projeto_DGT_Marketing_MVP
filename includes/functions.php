<?php
// Funções de validação
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

// Funções de log
function logError($message) {
    $logFile = __DIR__ . '/../logs/error_' . date('Y-m-d') . '.log';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
}

function logActivity($message) {
    $logFile = __DIR__ . '/../logs/activity_' . date('Y-m-d') . '.log';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
}

// Funções de segurança CSRF
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

function validateCSRF($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

function renderCSRFField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Funções úteis
function formatPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) == 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
    } elseif (strlen($phone) == 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
    }
    return $phone;
}

function getUserIP() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER)) {
            $ip = explode(',', $_SERVER[$key])[0];
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 3600) {
    $cacheKey = "rate_limit_$identifier";
    $attempts = $_SESSION[$cacheKey] ?? ['count' => 0, 'first_attempt' => time()];
    
    $currentTime = time();
    
    if ($currentTime - $attempts['first_attempt'] > $timeWindow) {
        $attempts = ['count' => 0, 'first_attempt' => $currentTime];
    }
    
    $attempts['count']++;
    $_SESSION[$cacheKey] = $attempts;
    
    return $attempts['count'] <= $maxAttempts;
}
