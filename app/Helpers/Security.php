<?php

namespace App\Helpers;

class Security {
    /**
     * Sanitiza qualquer string para exibição segura em HTML (Prevenção de XSS)
     */
    public static function escape(?string $value): string {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Gera e devolve o token CSRF da sessão ativa
     */
    public static function generateCsrfToken(): string {
        Session::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida se o token CSRF recebido no POST coincide com o da sessão
     */
    public static function validateCsrfToken(?string $token): bool {
        Session::start();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Gera hash seguro para palavras-passe (bcrypt)
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verifica palavra-passe contra o hash guardado
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}
