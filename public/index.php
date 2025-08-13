<?php 
    // 1) Tomar la ruta sin query string
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // 2) Detectar el "base path" real donde vive index (por ej. /public)
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // típicamente: /public

    // 3) Quitar el base path y slashes sobrantes -> obtener el FRS
    $frs = preg_replace('#^' . preg_quote($basePath, '#') . '/?#', '', $uri);
    $frs = trim($frs, '/'); 
    if ($frs === '') { 
        $frs = 'inicio'; // raíz => inicio
    }

    // === Bootstrap ===
    $rutaBase = realpath(__DIR__ . '/../');
    include $rutaBase . '/app/core/init.php';

    $conectionInstance = ConnectionBD::getInstance();
    $conexion = $conectionInstance->getConnection();

    $routeInstance = new RouteController($conexion);

    // 4) Pasar SOLO el FRS (no la REQUEST_URI completa)
    $routeInstance->routeHandler($frs);
    $vista = $routeInstance->getView();

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
            include_once ROUTE_VIEWS . '/' . $vista . '.php';

    ?>


</body>
</html>