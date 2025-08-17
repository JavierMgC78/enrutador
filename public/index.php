<?php
declare(strict_types=1);

// 1) BOOT
$rutaBase = realpath(__DIR__ . '/../');
require_once $rutaBase . '/app/core/init.php';

// 2) CONEXIÓN BD
$conectionInstance = ConnectionBD::getInstance();
$conexion = $conectionInstance->getConnection();

// 3) CONTROLADOR
//require_once $rutaBase . '/app/controllers/ViewsController.php'; // ajusta ruta/clase si usas otro nombre
$vc = new ViewsController($conexion);

// 4) PROCESAR REQUEST (extract -> sanitize -> assign)
$vc->viewHandler(); // sin args: usa REQUEST_URI internamente

// 5) RESOLVER VISTA Y STATUS
$vista = $vc->getView();
$meta  = $vc->getCurrentViewMeta();
print_r($meta); // para depuración, puedes quitarlo después
$viewsPath = defined('ROUTE_VIEWS') ? ROUTE_VIEWS : ($rutaBase . '/app/vistas');
$file = $viewsPath . '/' . $vista . '.php';

if ($vista === '404') {
    http_response_code(404);
} elseif ($vista === 'login') {
    // Opciones:
    // http_response_code(401); // si solo muestras la vista
    // header('Location: /login'); exit; // si prefieres redireccionar
}

if (!is_file($file)) {
    // Salvaguarda final
    http_response_code(404);
    $file = $viewsPath . '/404.php';
}

// 6) META DINÁMICO
$title = 'Mi App';
$meta = $vc->getCurrentViewMeta();
if (!empty($meta['view_name'])) {
    $title = $meta['view_name'] . ' | Enrutador';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php include_once $viewsPath . '/partials_meta.php'; ?>
</head>
<body>

    <header>
        <?php include_once $viewsPath . '/partials_headerNav.php'; ?>
    </header>
    <main class="site-main">
        <?php include $file; ?>
    </main>


</body>
</html>