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
    include_once $rutaBase . '/app/controladores/Login.php';

/* =========================
        INSTANCIAS y VARIABLES  GENERALES
    ======================== */
    $conectionInstance = ConnectionBD::getInstance();
    $conexion = $conectionInstance->getConnection();
    $routeInstance = new RouteController($conexion);  
    $routeInstance->routeHandler($GetRequest);    
    $vista = $routeInstance->getView();
  


    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo"esta vista:" . $vista;
  echo password_hash('1234', PASSWORD_DEFAULT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    

    // Autenticación básica (puedes reemplazarlo con validación de BD)
    $aut = new Login($conexion);
    $data = $aut->authenticate($usuario, $contrasena);
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "<pre>";
    echo("el data" . $data);
    echo "</pre>";


    if ($aut->authenticate($usuario, $contrasena)) {
        setcookie('usuario', $usuario, time() + 3600, '/'); // 1 hora
        //header('Location: dashboard.php');
        
        exit;
    } else {
        echo "<h1>el usuario  NO  existe</h1>";
    }
}

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