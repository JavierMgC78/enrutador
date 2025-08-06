<?php

class RouteController {

    private $whiteList; // lista blanca de dstinos
    private $view; // archivo destino sin extensión
    private $needsLogin; // indica si la ruta requiere login
    private $elementIndex;

    public function __construct($conexion) {
        $WLSql = "SELECT ruta, requiere_login FROM rutas_permitidas WHERE activo = 1";
        $stmt = $conexion->prepare($WLSql);
        $stmt->execute();
        
        $this->whiteList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /* =========================
        GETTERS
    ======================== */

    public function getView()
    {
        return $this->view;
    }

    public function getNeedsLogin()
    {
        return $this->needsLogin;
    }





public function routeHandler($frs) 
{
    //definir si el frs tiene parametros 
    $withParams = $this->frsIncludesParams($frs);

    if ($withParams) {
        // Si tiene parámetros, extraer la ruta base
        list($view, $params) = explode('?', $frs, 2);

    }else{
        $this->view = $frs;
        $this->isViewInWhiteList($this->view);
    }


}

public function frsIncludesParams($frs)
{
    return strpos($frs, '?') !== false;
}



public function isViewInWhiteList($view) 
{
   
    $found = false;

    $view = trim($view, '/');

    foreach ($this->whiteList as $index => $route) {
        if ($route['ruta'] === $view) {
            $this->elementIndex = $index;

            // Si requiere login y no hay cookie, ir a login
            if (($route['requiere_login']) && empty($_COOKIE['usuario_id'])) {
                $this->view = 'login';
            } else {
                $this->view = $view;
            }
            $found = true;
            break; // ya se encontró, salimos del bucle
        }
    }
    if (!$found) {
        $this->view = '404';
    }

}





    public function vista($uri ) {
       // Limpiar la ruta y eliminar el prefijo `/public/` si es necesario
        $view = trim(parse_url($uri, PHP_URL_PATH), '/');
        $view = str_replace('/public', '', $uri);
        if($uri){
            if (file_exists(RUTA_VISTAS . '/' . $uri . '.php')){
                $this->view = $uri;


                
            }else{
                $this->view = 404;
            }

        }else{
            $this->view = 404;
        }
            
    }

    public function clean_route($destiny) {
        if ($destiny){
            // Limpiar la ruta (remover `/public/` y "/"si es necesario)
            $view = str_replace('/public', '', $destiny);
            $view = trim($view, '/');

            // validar la vista
            $this->validate_destiny($view);

            //$this->view = $view;
            
        }
        $this->view = 'inicio';
    }

    public function validate_destiny($destiny) {
        $routeExist = false;
        // Verificar si la ruta existe en la lista blanca
        $this->elementIndex = null;
        $this->view = '404'; // Valor por defecto
        $destiny = trim($destiny); // Por si acaso
        foreach ($this->whiteList as $index => $route) {
        if ($route['ruta'] === $destiny) {
            $this->elementIndex = $index;

            // Si requiere login y no hay cookie, ir a login
            if (!empty($route['requiere_login']) && empty($_COOKIE['usuario_id'])) {
                $this->view = 'login';
            } else {
                $this->view = $destiny;
            }

            break; // ya se encontró, salimos del bucle
        }
    }
    }
}