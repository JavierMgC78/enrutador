<?php 
    $rutaBase = realpath(__DIR__ . '/../');
    include ($rutaBase . '/core/init.php');
    include_once$rutaBase . '/routes/routes_controller.php';
    //echo 'ruta_raiz'. RUTA_RAIZ;
    echo 'ruta_vistas'. RUTA_VISTAS;

    // Obtener la ruta desde la URL
    $request = $_SERVER['REQUEST_URI'];
    echo "rerquest_uri" . $request;

    // Limpiar la ruta (remover `/public/` si es necesario)
    $request = str_replace('/public', '', $request);

    $request = trim($request, '/');
    //echo $request;
    
    $vista = $request ?: 'inicio';

    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once RUTA_VISTAS . '/template_meta.php'; ?>
</head>
<body>
    <header>
        <?php include_once (RUTA_VISTAS . '/template_nav.php'); ?>
    </header>
</body>
</html>
<?php
    
    $archivo = new RouteController();
    $vista = $archivo->mostrarVista($vista);
    include_once RUTA_VISTAS . '/' . $vista . '.php';

?>


</body>
</html>