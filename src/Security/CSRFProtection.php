<?php
namespace DGTMarketing\Security;

class CSRFProtection {
    private $tokenLifetime = 3600; // 1 hora

    public function __construct() {
        // Verificar se sessão já não foi iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function generateToken() {
        $token = bin2hex(random_bytes(32));
        $expiration = time() + $this->tokenLifetime;
        
        $_SESSION['csrf_tokens'][$token] = $expiration;
        return $token;
    }

    public function validateToken($token) {
        if (!isset($_SESSION['csrf_tokens'][$token])) {
            return false;
        }

        $expiration = $_SESSION['csrf_tokens'][$token];
        
        if (time() > $expiration) {
            unset($_SESSION['csrf_tokens'][$token]);
            return false;
        }

        unset($_SESSION['csrf_tokens'][$token]);
        return true;
    }

    public function renderTokenField() {
        $token = $this->generateToken();
        return sprintf(
            '<input type="hidden" name="csrf_token" value="%s">',
            $token
        );
    }
}
