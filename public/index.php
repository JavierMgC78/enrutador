<?php 
    // Obtener la ruta desde la URL
    $GetRequest = $_SERVER['REQUEST_URI'];
    echo "<br>REQUEST_URI: " . $GetRequest;

    /* ===================
            INCLUDES
    ==================== */
    $rutaBase = realpath(__DIR__ . '/../');
    echo "RUTA BASE" . $rutaBase;

    include_once $rutaBase . '/app/modelos/ConecctionBD.php';
    include $rutaBase . '/core/init.php';
    include_once $rutaBase . '/core/Functions.php';
    include_once $rutaBase . '/routes/Route_controller.php';

/* =========================
        INSTANCIAS y VARIABLES  GENERALES
    ======================== */
    $conectionInstance = ConnectionBD::getInstance();
    $conexion = $conectionInstance->getConnection();

    $routeInstance = new RouteController($conexion);
    /* =========================
        VARIABLES GENERALES
    ======================== */
    
    $routeInstance->routeHandler($GetRequest);    
    $vista = $routeInstance->getView();
  
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo"esta vista:" . $vista;

    
    

    
  
    

  
  

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
    // Cargar la vista correspondiente
        include_once RUTA_VISTAS . '/' . $vista . '.php';

?>


</body>
</html>