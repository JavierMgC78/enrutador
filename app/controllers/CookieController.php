<?php
declare(strict_types=1);

class CookieController
{
    private string $name;
    private array $options;

    /**
     * @param string $name    Nombre de la cookie de sesión (ej. 'app_session')
     * @param array  $options Opciones para setcookie (path, domain, secure, httponly, samesite)
     */
    public function __construct(string $name, array $options = [])
    {
        $defaults = [
            'path'     => '/',
            'domain'   => '', // si usas subdominios, ponlo aquí
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax', // 'Strict' si todo está en el mismo sitio
        ];
        $this->name    = $name;
        $this->options = array_replace($defaults, $options);
    }

    /**
     * ÚNICA responsabilidad aquí: ¿la cookie existe y no está vacía?
     */
    public function isCookieExist(string $cookie_name = 'app_session'): bool
    {
        if (!isset($_COOKIE[$this->name])) {
            return false;
        }
        $val = trim((string)$_COOKIE[$this->name]);
        // Tipos de valores “basura” que algunos entornos dejan al borrar
        if ($val === '' || $val === 'deleted' || $val === 'null' || $val === 'undefined') {
            return false;
        }
        return true;
    }

    /**
     * Lee el valor crudo de la cookie (o null si no hay).
     */
    public function read(): ?string
    {
        return $this->isCookieExist() ? trim((string)$_COOKIE[$this->name]) : null;
    }

    /**
     * Crea/actualiza la cookie con TTL en segundos.
     */
    public function set(string $value, int $ttlSeconds): bool
    {
        $opts = $this->options;
        $opts['expires'] = time() + max(0, $ttlSeconds);
        return setcookie($this->name, $value, $opts);
    }

    /**
     * Borra la cookie de forma segura.
     */
    public function delete(): bool
    {
        $opts = $this->options;
        $opts['expires'] = time() - 3600;
        unset($_COOKIE[$this->name]); // sincroniza el superglobal
        return setcookie($this->name, '', $opts);
    }

    /**
     * (Opcional pero útil) Verifica forma del token sin tocar BD
     * Ej.: 64 hex (256 bits).
     */
    public function isTokenWellFormed(?string $candidate = null, string $pattern = '/^[A-Fa-f0-9]{64}$/'): bool
    {
        $token = $candidate ?? $this->read();
        if ($token === null) return false;
        return (bool)preg_match($pattern, $token);
    }
}