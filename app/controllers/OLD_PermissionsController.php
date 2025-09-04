<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PDOException;   // <— importa la excepción

class PermissionsController
{
    public function __construct(private PDO $conexion) {}

    public function validateDataAccess(string $user, string $password): PDO
    {
        $user = trim($user);
        if ($user === '' || $password === '') {
            $this->deny('Usuario y/o contraseña requeridos.');
        }

        try {
            $sql = 'SELECT id, usuario, contrasena, estatus, rol
                    FROM usuarios_asigna
                    WHERE usuario = :usuario
                    LIMIT 1';
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':usuario' => $user]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->deny('Error al validar. Intenta de nuevo.');
        }

        // Usuario no encontrado o password incorrecto
        if (!$row || !password_verify($password, $row['contrasena'])) {
            usleep(random_int(120000, 300000)); // pequeñísimo delay anti-fuerza bruta
            $this->deny('Credenciales inválidas.');
        }

        // Cuenta deshabilitada (si manejas estatus)
        if (isset($row['estatus']) && (int)$row['estatus'] !== 1) {
            $this->deny('Cuenta deshabilitada.');
        }

        // === Autenticación OK ===
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);

        $_SESSION['auth'] = [
            'user_id'  => (int)$row['id'],
            'username' => $row['usuario'],
            'role'     => $row['rol'] ?? 'user',
            'csrf'     => bin2hex(random_bytes(32)),
        ];

        // Cookie de sesión (opcional)
        $token = hash('sha256', $row['id'] . bin2hex(random_bytes(32)) . microtime(true));
        setcookie('session_token', $token, [
            'expires'  => time() + 60*60*8, // 8h
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        header('Location: /panel'); // <-- cambia a tu ruta de inicio
        exit;
    }

    private function deny(string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['flash_error'] = $message;
        header('Location: /login'); // <-- cambia a tu ruta de login
        exit;
    }





}