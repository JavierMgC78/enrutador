<?php
// configuracciones y valores de uso general
declare(strict_types=1);
$rutaBase = realpath(__DIR__ . '/../');

// 1) INIT
require_once $rutaBase . '/app/core/init.php';
// ) INIT_CONTROLLERS_INSTANCE
require_once $rutaBase . '/app/core/init_controllers_instance.php';
// 3) INIT_ALLOWED_VIEWS
require_once $rutaBase . '/app/core/init_allowed_views.php';

$allowedViews = $vc->requestAllowedViews();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){ 
    $user = $_POST['user'] ?? '';
    $password = $_POST['password'] ?? '';

    $fc->handler(); // sin args: usa REQUEST_URI internamente

    

}




$title = 'Mi App';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php include_once ROUTE_VIEWS. '/partials_meta.php'; ?>
</head>
<body>

    <header>
        <?php include_once ROUTE_VIEWS . '/partials_headerNav.php'; ?>
    </header>
    <main class="site-main">
        <?php include ROUTE_VIEWS . '/' . $fc->getView() . '.php'; ?>
    </main>


</body>
</html>