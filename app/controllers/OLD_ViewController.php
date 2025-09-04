<?php
/**
 * ===================================================
 * Índice de métodos - ViewController
 * ===================================================
 * 1. __construct(PDO $conexion)
 * 2. getView(): string- Devuelve la vista resuelta (ej. inicio, dashboard, login, 404, etc.).
 * 3. getCurrentViewMeta(): array - Devuelve los metadatos completos de la vista actual (row de allowed_views). 
 * 5. getViewsWhiteList(): array
 * 6. viewHandler(?string $requestUri = null): void - Método principal. Extrae el FRS desde la URI, busca en la lista blanca y aplica reglas:
 * 7. extractFrs(?string $requestUri): string
 * 8. loadWhiteList(): void
 * 9. hasValidSession(): bool
 * 10. getCurrentUser(): ?array
 * 11. hasFineAccess(int $viewId, int $userId): bool
 * 12. set404(): void
 * 13. set403(): void
 */



class ViewController 
{

    private $viewsWhiteList; // lista blanca de dstinos
    private $view; // archivo destino sin extensión
    private $needsLogin; // indica si la ruta requiere login
    private $zanitizedFrs;
    private $currentViewMeta;
    private $viewIncluded = false;
    private $cookieSession = false;


    public function __construct($conexion) {
        // obtener lista blanca
        $WLSql = "SELECT view_name, require_login, min_access_level, fine_access FROM allowed_views WHERE active = 1";
        $stmt = $conexion->prepare($WLSql);
        $stmt->execute();
        
        $this->viewsWhiteList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Summary of getview
     * @return array|int|string
     */
    public function getview()
    {
        return $this->view;
    }

    /**
     * Summary of getViewsWhiteList
     */
    public function getViewsWhiteList()
    {
        return $this->viewsWhiteList;
    }

    /**
     * VIEWHANDLER
     * @param mixed $requestUri
     * @return void
     */
    public function viewHandler($requestUri = null) 
    {
        $frs = $this->extractFrs($requestUri);

        //definir si el frs tiene parametros 
        $withParams = $this->frsIncludesParams($frs);
        
        if ($withParams) {
            // Si tiene parámetros, extraer la ruta base
            //list($view, $params) = explode('?', $frs, 2);

        }else{
            // limpiar y normalizar el FRS
            //$this->zanitizedFrs = $this->sanitizeFrs($frs);
        
            if($this->frsIncludeInWhiteList($frs))
            {
                $this->view = $frs; // asignar y devolver vista lista para mostrar  
            } else {
                $this->view = 404;
            }
        }
    }


    /**
     * Summary of isFrsInWhiteList
     * @param mixed $frs
     * @return void
     */
    public function isFrsInWhiteList($frs) 
    {
        $found = false;

        $view = trim($frs, '/');

        foreach ($this->viewsWhiteList as $index => $route) {
            if ($route['view_name'] === $view) {
            

                // Si requiere login y no hay cookie, ir a login
                if (($route['require_login']) || empty($_COOKIE['usuario_id'])) {
                    $this->view = 'login';
                } else {
                    $this->view = 'login';
                }
                $found = true;
                break; // ya se encontró, salimos del bucle
            }
        }
        if (!$found) {
            $this->view = '404';
        }

    }



    /**
     * Summary of frsIncludesParams
     * @param mixed $frs
     * @return bool
     */
    public function frsIncludesParams($frs)
    {
        return strpos($frs, '?') !== false;
    }


    /**
     * Summary of frsIncludeInWhiteList
     * @param mixed $frs
     * @return bool
     */
    public function frsIncludeInWhiteList($frs)
    {
        if (!is_array($this->viewsWhiteList) || empty($this->viewsWhiteList)) {
            return false;
        }

        // Recibe slug canónico (no sanitizar aquí)
        $slug = (string) $frs;

        foreach ($this->viewsWhiteList as $row) {
            // Preferir 'frs'; si no existe, comparar contra 'view_name'
            $candidate = '';
            if (isset($row['frs'])) {
                $candidate = (string) $row['frs'];
            } elseif (isset($row['view_name'])) {
                $candidate = (string) $row['view_name'];
            }

            // Comparación case-insensitive
            if ($candidate !== '' && strtolower($candidate) === strtolower($slug)) {
                $this->currentViewMeta = $row; // guardar metadatos de la vista
                return true;
            }
        }

    return false;
    }

    /**
     * Summary of sanitizeFrs
     * @param mixed $frs
     * @return array|string|null
     */
    private function sanitizeFrs($frs)
    {
        // 1) String seguro
        $s = (string) $frs;

        // 2) Decodifica URL (e.g. %20)
        $s = urldecode($s);

        // 3) Quitar querystring por si viene incluido
        $qpos = strpos($s, '?');
        if ($qpos !== false) {
            $s = substr($s, 0, $qpos);
        }

        // 4) Recortar espacios y slashes de extremos
        $s = trim($s, " \t\n\r\0\x0B/");

        // 5) Transliterar acentos a ASCII si es posible (ñ->n, á->a, etc.)
        if (function_exists('iconv')) {
            $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            if ($tmp !== false) {
                $s = $tmp;
            }
        }

        // 6) Reemplazar espacios y underscores por guiones
        $s = str_replace([' ', '_'], '-', $s);

        // 7) Minúsculas
        $s = function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);

        // 8) Eliminar traversal y cualquier char no [a-z0-9-]
        $s = preg_replace('#(?:\.+/|/+\.+)#', '', $s);
        $s = preg_replace('~[^a-z0-9\-]~', '', $s);

        // 9) Colapsar guiones repetidos
        $s = preg_replace('~-{2,}~', '-', $s);

        // 10) Fallback si quedó vacío
        if ($s === '' ) {
            $s = 'inicio';
        }

        return $s;
    }


    /**
     * Summary of extractFrs
     * @param mixed $path
     * @return array|string|null
     */
    public function extractFrs(?string $path = null): string
    {
        // 1) Ruta origen
        if ($path === null) {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        }

        // 2) Base path (p. ej. /public)
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

        // 3) Quitar base path y slashes
        $rel = preg_replace('#^' . preg_quote($basePath, '#') . '/?#', '', $path);
        $rel = trim($rel, '/');
        if ($rel === '') {
            $rel = 'inicio';
        }

        // 4) Única fuente de verdad
        return $this->sanitizeFrs($rel);
    }
}