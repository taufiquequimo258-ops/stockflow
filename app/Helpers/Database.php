<?php

namespace App\Helpers;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $options);
            } catch (PDOException $e) {
                // Se a base de dados não existir (Erro 1049), tentar criar automaticamente via schema.sql
                if ($e->getCode() == 1049 || str_contains($e->getMessage(), 'Unknown database')) {
                    try {
                        $rootDsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
                        $rootPdo = new PDO($rootDsn, $config['username'], $config['password'], $options);
                        $schemaFile = __DIR__ . '/../../database/schema.sql';
                        if (file_exists($schemaFile)) {
                            $sql = file_get_contents($schemaFile);
                            $rootPdo->exec($sql);
                            self::$instance = new PDO($dsn, $config['username'], $config['password'], $options);
                            return self::$instance;
                        }
                    } catch (PDOException $ex) {
                        die("
                        <div style='font-family:Segoe UI, sans-serif; padding:25px; color:#721c24; background:#f8d7da; border:1px solid #f5c6cb; border-radius:10px; max-width:650px; margin:50px auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                            <h3 style='margin-top:0;'><i class='fa-solid fa-triangle-exclamation'></i> Falha ao Inicializar a Base de Dados</h3>
                            <p>O MySQL está ligado, mas ocorreu um erro ao criar a base de dados automaticamente.</p>
                            <p><strong>Detalhes:</strong> " . htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>
                        </div>");
                    }
                }

                // Se o MySQL estiver desligado ou com credenciais incorretas
                die("
                <div style='font-family:Segoe UI, sans-serif; padding:25px; color:#721c24; background:#f8d7da; border:1px solid #f5c6cb; border-radius:10px; max-width:650px; margin:50px auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                    <h3 style='margin-top:0;'>⚠️ MySQL Desligado ou Indisponível</h3>
                    <p>A aplicação não conseguiu conectar ao servidor <strong>MySQL</strong>.</p>
                    <hr style='border:0; border-top:1px solid #f5c6cb;'>
                    <h4 style='margin-bottom:10px;'>Como Resolver em 2 Passos:</h4>
                    <ol style='line-height:1.6;'>
                        <li>Abra o <strong>XAMPP Control Panel</strong>.</li>
                        <li>Clique em <strong>Start</strong> ao lado do módulo <strong>MySQL</strong>.</li>
                    </ol>
                    <p>Depois de iniciar o MySQL, basta <strong>recarregar esta página</strong> no seu navegador!</p>
                    <details style='margin-top:15px; font-size:0.85em; color:#555;'>
                        <summary>Detalhe técnico do erro</summary>
                        <code>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</code>
                    </details>
                </div>");
            }
        }
        return self::$instance;
    }
}
