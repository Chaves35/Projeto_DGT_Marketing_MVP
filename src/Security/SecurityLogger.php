<?php
namespace DGTMarketing\Security;

class SecurityLogger {
    public static function logCSRFAttempt($details) {
        $logEntry = sprintf(
            "[%s] CSRF Attempt - Details: %s\n",
            date('Y-m-d H:i:s'),
            json_encode($details)
        );
        
        file_put_contents(
            __DIR__ . '/../../logs/security/csrf_attempts.log', 
            $logEntry, 
            FILE_APPEND
        );
    }

    public static function logSecurityEvent($type, $details) {
        $logEntry = sprintf(
            "[%s] %s - Details: %s\n",
            date('Y-m-d H:i:s'),
            $type,
            json_encode($details)
        );
        
        file_put_contents(
            __DIR__ . '/../../logs/security/events.log', 
            $logEntry, 
            FILE_APPEND
        );
    }
}
