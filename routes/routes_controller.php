<?php

class RouteController {


    public function mostrarVista($uri) {
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');
        if (file_exists(RUTA_VISTAS . '/' . $uri . '.php')){
            return $uri;
        }else{
            return 404;
        }


    }
}