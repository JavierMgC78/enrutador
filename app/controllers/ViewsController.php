<?php
declare(strict_types=1);

class ViewsController
{
    private const DEFAULT_VIEW   = 'inicio';
    private const NOT_FOUND_VIEW = '404';
    private const LOGIN_VIEW     = 'login'; // cámbiala si usas otro nombre

    /** @var array<int,array<string,mixed>> */
    private array $viewsWhiteList = [];     // filas de allowed_views
    /** @var array<string,array<string,mixed>> */
    private array $viewsIndex     = [];     // slug => meta (índice O(1))

    private string $view          = self::DEFAULT_VIEW;
    private bool   $inWhiteList   = false;
    /** @var array<string,mixed>|null */
    private ?array $currentViewMeta = null;

    public function __construct(PDO $conexion)
    {
        // Carga whitelist desde DB (ajusta nombres de columnas si difieren)
        $sql = "SELECT id, view_name, frs, require_login, min_access_level, fine_access, active
                FROM allowed_views
                WHERE active = 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $this->viewsWhiteList = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Construye índice slug => meta (usa 'frs' si existe; si no, 'view_name')
        foreach ($this->viewsWhiteList as $row) {
            $slug = strtolower((string)($row['frs'] ?? $row['view_name'] ?? ''));
            if ($slug !== '') {
                $this->viewsIndex[$slug] = $row;
            }
        }
    }

    /**
     * Punto de entrada del router por request.
     * Pipeline: extract -> sanitize -> assign (y aquí puedes añadir políticas de acceso).
     */
    public function handler(?string $requestUri = null): void
    {
        // 1) Extraer FRS tal como viene en la URL
        $frs = $this->extractFrs($requestUri);

        // 2) Sanitizar para obtener el slug que usará la comparación
        $sanitizedFrs = $this->sanitizeFrs($frs);

        // 3) Asignar vista (única fuente de verdad para $this->view)
        $this->assignView($sanitizedFrs);

        // 4) (Opcional) Reglas de acceso: si requiere login y no está logueado -> LOGIN_VIEW
        if ($this->inWhiteList
            && isset($this->currentViewMeta['require_login'])
            && (int)$this->currentViewMeta['require_login'] === 1
            && !$this->isLoggedIn()) {
            $this->view = self::LOGIN_VIEW;
        }
    }

    /**
     * Extrae el FRS desde la request (sin tocar BD ni acceso).
     * No se encarga del 404: solo extrae.
     */
    public function extractFrs(?string $path = null): string
    {
        if ($path === null) {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        }

        // Detecta basePath (p. ej. /public)
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

        // Quita basePath y slashes
        $rel = preg_replace('#^' . preg_quote($basePath, '#') . '/?#', '', $path);
        $rel = trim((string)$rel, '/');

        return $rel === '' ? self::DEFAULT_VIEW : $rel;
    }

    /**
     * Normaliza el FRS a un slug comparable con allowed_views.
     * Mantén esta función como “única verdad” de normalización.
     */
    public function sanitizeFrs(string $frs): string
    {
        $frs = strtolower($frs);
        $frs = explode('/', $frs, 2)[0];          // FRS = primer segmento
        // Permite letras, números, guion, guion bajo y dos puntos (ajusta a tu política)
        $frs = preg_replace('~[^a-z0-9\-\_:]~', '', $frs) ?? '';

        // Bloquea traversal o vacíos sospechosos
        if ($frs === '' || $frs === '.' || $frs === '..') {
            return self::NOT_FOUND_VIEW;
        }
        return $frs;
    }

    /**
     * Verifica si el slug está en whitelist (método puro: no cambia estado).
     */
    public function isInWhiteList(string $slug): bool
    {
        return isset($this->viewsIndex[$slug]);
    }

    /**
     * Asigna la vista y guarda metadatos. ÚNICA fuente de verdad para $this->view.
     */
    public function assignView(string $sanitizedFrs): void
    {
        $meta = $this->viewsIndex[$sanitizedFrs] ?? null;

        $this->inWhiteList     = ($meta !== null);
        $this->currentViewMeta = $meta;
        $this->view            = $this->inWhiteList ? $sanitizedFrs : self::NOT_FOUND_VIEW;
    }

    /**
     * Chequeo mínimo de login (adáptalo a tu sistema real).
     */
    private function isLoggedIn(): bool
    {
        return !empty($_COOKIE['usuario_id'] ?? null);
    }

    // -------- Getters útiles --------
    public function getView(): string { return $this->view; }
    /** @return array<string,mixed>|null */
    public function ViewMetaData(): ?array { return $this->currentViewMeta; }
    public function isWhitelisted(): bool { return $this->inWhiteList; }
}