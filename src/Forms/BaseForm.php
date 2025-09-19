<?php
namespace DGTMarketing\Forms;

class BaseForm {
    protected static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    protected static function validateCSRF($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function renderCSRFField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
    
    public static function getCSRFToken() {
        return self::generateCSRFToken();
    }
}
