<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;

/**
 * PermissionController
 * Decide si una vista puede renderizarse o no, según metadata de la vista,
 * el contexto del usuario y el contexto de la petición.
 *
 * Contrato de salida (Decision Object):
 * [
 *   'decision'    => 'ALLOW'|'REDIRECT_LOGIN'|'DENY_403'|'NOT_FOUND_404'|'RATE_LIMIT_429',
 *   'http_status' => 200|302|403|404|429,
 *   'reason_code' => string|null,
 *   'redirect_to' => string|null
 * ]
 */
final class PermissionController
{
    public const ALLOW           = 'ALLOW';
    public const REDIRECT_LOGIN  = 'REDIRECT_LOGIN';
    public const DENY_403        = 'DENY_403';
    public const NOT_FOUND_404   = 'NOT_FOUND_404';
    public const RATE_LIMIT_429  = 'RATE_LIMIT_429';

    // Razones estándar (útiles para logs/UX)
    public const R_NO_SUCH_VIEW       = 'NO_SUCH_VIEW';
    public const R_VIEW_INACTIVE      = 'VIEW_INACTIVE';
    public const R_NEED_LOGIN         = 'NEED_LOGIN';
    public const R_INVALID_SESSION    = 'INVALID_SESSION';
    public const R_USER_DISABLED      = 'USER_DISABLED';
    public const R_LOW_LEVEL          = 'LOW_LEVEL';
    public const R_MISSING_FINE       = 'MISSING_FINE_ACCESS';
    public const R_MAINTENANCE        = 'MAINTENANCE';
    public const R_TENANT_MISMATCH    = 'TENANT_MISMATCH';
    public const R_RATE_LIMITED       = 'RATE_LIMITED';

    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Punto único de decisión.
     * @param array      $viewMeta   => { id, view_name, frs, require_login, min_access_level, fine_access, active, ... }
     * @param array|null $userCtx    => { isAuthenticated, user_id, access_level, status, session_token, ... } o null si no hay sesión
     * @param array      $reqCtx     => { ip, ua, method, requested_frs, now, ... }
     */
    public function permissionHandler(array $viewMeta, ?array $userCtx, array $reqCtx): array
    {
        // 1) Existencia/estado
        if (empty($viewMeta)) {
            return $this->decision(self::NOT_FOUND_404, 404, self::R_NO_SUCH_VIEW);
        }
        if ((int)($viewMeta['active'] ?? 0) !== 1) {
            return $this->decision(self::NOT_FOUND_404, 404, self::R_VIEW_INACTIVE);
        }

        // 2) (Opcional) Política por método HTTP
        // if (($reqCtx['method'] ?? 'GET') !== 'GET') { ... }

        $requiresLogin = (int)($viewMeta['require_login'] ?? 0) === 1;

        // 3) Falta de sesión si la vista la requiere
        if ($requiresLogin && (empty($userCtx) || ($userCtx['isAuthenticated'] ?? false) !== true)) {
            $next = '/' . ltrim((string)($viewMeta['frs'] ?? ''), '/');
            return $this->decision(self::REDIRECT_LOGIN, 302, self::R_NEED_LOGIN, '/login?next=' . $next);
        }

        // 4) Validación de sesión/usuario (si autenticado)
        if (!empty($userCtx) && ($userCtx['isAuthenticated'] ?? false) === true) {
            if (!$this->checkSession($userCtx)) {
                $next = '/' . ltrim((string)($viewMeta['frs'] ?? ''), '/');
                return $this->decision(self::REDIRECT_LOGIN, 302, self::R_INVALID_SESSION, '/login?next=' . $next);
            }
            if (($userCtx['status'] ?? 'active') !== 'active') {
                return $this->decision(self::DENY_403, 403, self::R_USER_DISABLED);
            }
        }

        // 5) RBAC: nivel mínimo
        $minLevel = (int)($viewMeta['min_access_level'] ?? 0);
        $userLvl  = (int)($userCtx['access_level'] ?? 0);
        if ($requiresLogin && $userLvl < $minLevel) {
            return $this->decision(self::DENY_403, 403, self::R_LOW_LEVEL);
        }

        // 6) Control fino por usuario (si está activo)
        $fine = (int)($viewMeta['fine_access'] ?? 0) === 1;
        if ($requiresLogin && $fine) {
            $viewId = (int)($viewMeta['id'] ?? 0);
            $userId = (int)($userCtx['user_id'] ?? 0);
            if (!$this->hasFineAccess($viewId, $userId)) {
                return $this->decision(self::DENY_403, 403, self::R_MISSING_FINE);
            }
        }

        // 7) (Opcional) Mantenimiento / tenant / rate limit
        // if ($this->isMaintenance($viewMeta)) { return $this->decision(self::DENY_403, 403, self::R_MAINTENANCE); }
        // if ($this->tenantMismatch($viewMeta, $userCtx)) { return $this->decision(self::DENY_403, 403, self::R_TENANT_MISMATCH); }
        // if ($this->isRateLimited($reqCtx, $userCtx)) { return $this->decision(self::RATE_LIMIT_429, 429, self::R_RATE_LIMITED); }

        // 8) Todo OK
        return $this->decision(self::ALLOW, 200, null);
    }

    /** Verifica que el token/cookie sea válido en BD (vigencia, no revocado, usuario activo). */
    private function checkSession(array $userCtx): bool
    {
        // TODO: Implementar la validación real contra BD.
        // Ejemplo típico:
        // $sql = "SELECT 1 FROM tokens_usuario WHERE usuario_id = ? AND token = ? AND expiracion > NOW() AND revocado = 0 LIMIT 1";
        // $stmt = $this->db->prepare($sql);
        // $stmt->execute([$userCtx['user_id'], $userCtx['session_token']]);
        // return (bool) $stmt->fetchColumn();
        return true; // Placeholder hasta que conectes con tu tabla real
    }

    /** Control fino: ¿existe (view_id,user_id) en la tabla puente? */
    private function hasFineAccess(int $viewId, int $userId): bool
    {
        // TODO: Ajusta el nombre de tu tabla puente si difiere
        $sql = "SELECT 1 FROM allowed_view_user_access WHERE view_id = ? AND user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$viewId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Construye el objeto de decisión estándar. */
    private function decision(string $decision, int $status, ?string $reason = null, ?string $redirectTo = null): array
    {
        return [
            'decision'    => $decision,
            'http_status' => $status,
            'reason_code' => $reason,
            'redirect_to' => $redirectTo,
        ];
    }
}