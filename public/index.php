<?php 
    // 1) CARFAR ARCHIVO DE CONFIGURACIÓN INIT
    $rutaBase = realpath(__DIR__ . '/../');
    include $rutaBase . '/app/core/init.php';


    // 2 crear conexion a la base de datos
    $conectionInstance = ConnectionBD::getInstance();
    $conexion = $conectionInstance->getConnection();

    // 3) OBTENER LISTA BLANCA DE VISTAS
    $viewControllerInstance = new ViewController($conexion);
    $viewsWhiteList = $viewControllerInstance->getViewsWhiteList();

    // 4) OBTENER FRS
    //Tomar la ruta sin query string
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Detectar el "base path" real donde vive index (por ej. /public)
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // típicamente: /public
    // Quitar el base path y slashes sobrantes -> obtener el FRS
    $frs = preg_replace('#^' . preg_quote($basePath, '#') . '/?#', '', $uri);
    $frs = trim($frs, '/'); 
    if ($frs === '') { 
        $frs = 'inicio'; // raíz => inicio
    }



    //$conectionInstance = ConnectionBD::getInstance();
    //$conexion = $conectionInstance->getConnection();
    // 4) INSTANCIAR VIEWCONTROLLER
    $viewInstance = new ViewController($conexion);
    

    $viewInstance->viewHandler($frs);
    // 5) OBTENER VISTA
    $vista = $viewInstance->getView();




    $routeInstance = new RouteController($conexion);

    // 4) Pasar SOLO el FRS (no la REQUEST_URI completa)
    $routeInstance->routeHandler($frs);
    
    //$vista = $routeInstance->getView();

    // 5) Login (si llega POST), antes de renderizar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = $_POST['usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';  

        $aut = new Login($conexion);
        $data = $aut->authenticate($usuario, $contrasena);




        if ($data['status']) {
            setcookie('usuario', $usuario, time() + 3600, '/'); // 1 hora
            $vista = $data['vista']; // por ejemplo 'dashboard'
            // Opcionalmente, podrías hacer un redirect 303 para evitar reenvío de formulario
            // header("Location: /{$vista}", true, 303); exit;
        }else {


        }
    }
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <?php include_once ROUTE_VIEWS . '/partials_meta.php'; ?>
    </head>
<body>
        <header>
            <?php include_once (ROUTE_VIEWS . '/partials_headerNav.php'); ?>
        </header>

    <?php
        // Cargar la vista correspondiente
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo $vista;
        include_once ROUTE_VIEWS . '/' . $vista . '.php';

    ?>


</body>
</html>