<?php

class ViewController {

    private $viewsWhiteList; // lista blanca de dstinos
    private $view; // archivo destino sin extensión
    private $needsLogin; // indica si la ruta requiere login

    public function __construct($conexion) {
        $WLSql = "SELECT view_name, require_login, fine_access FROM allowed_views WHERE active = 1";
        $stmt = $conexion->prepare($WLSql);
        $stmt->execute();
        
        $this->viewsWhiteList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getview()
    {
        return $this->view;
    }
    public function getViewsWhiteList()
    {
        return $this->viewsWhiteList;
    }

    
public function viewHandler($frs) 
{
    //definir si el frs tiene parametros 
    $withParams = $this->frsIncludesParams($frs);
    
    if ($withParams) {
        // Si tiene parámetros, extraer la ruta base
        list($view, $params) = explode('?', $frs, 2);

    }else{
        $this->view = $frs;
        $this->isFrsInWhiteList($this->view);
    }


}

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

public function frsIncludesParams($frs)
{
    return strpos($frs, '?') !== false;
}


}