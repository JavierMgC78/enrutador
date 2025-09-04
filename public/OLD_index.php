<?php
declare(strict_types=1);
use App\Controllers\PermissionsController;

// 1) BOOT
$rutaBase = realpath(__DIR__ . '/../');
require_once $rutaBase . '/app/core/init.php';

//2) VALIDAR SESSION
if ($_SERVER['REQUEST_METHOD'] === 'POST'){ 

$user = $_POST['user'] ?? '';
$password = $_POST['password'] ?? '';
//$pc = new PermissionsController($conexion);
//$pc->validateDataAccess($user, $password);
    
}


// ) PROCESAR REQUEST (extract -> sanitize -> assign)
$vc->viewHandler(); // sin args: usa REQUEST_URI internamente

// 6) RESOLVER VISTA Y STATUS
$view = $vc->getView();


// aqui va pc(permisionController)
$viewMetaData  = $vc->getCurrentViewMetaData();
print_r($viewMetaData); // para depuración, puedes quitarlo después
$viewsPath = defined('ROUTE_VIEWS') ? ROUTE_VIEWS : ($rutaBase . '/app/vistas');
$file = $viewsPath . '/' . $view . '.php';





if ($view === '404') {
    http_response_code(404);
} elseif ($view === 'login') {
    // Opciones:
    // http_response_code(401); // si solo muestras la vista
    // header('Location: /login'); exit; // si prefieres redireccionar
}

if (!is_file($file)) {
    // Salvaguarda final
    http_response_code(404);
    $file = $viewsPath . '/404.php';
}

// 7) META DINÁMICO
$title = 'Mi App';
$viewMetaData = $vc->getCurrentViewMetaData();
if (!empty($viewMetaData['view_name'])) {
    $title = $viewMetaData['view_name'] . ' | Enrutador';
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