<?php
// Ruta base donde están las vistas
$rutaVistas = realpath(__DIR__ . '/../vistas/');

// Obtener la vista desde $_GET y aplicar seguridad básica
$vistaSolicitada = isset($_GET['view']) ? basename($_GET['view']) : 'inicio.php';

// Ruta absoluta del archivo solicitado
$archivoVista = $rutaVistas . '/' . $vistaSolicitada;


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once '../vistas/template_meta.php'; ?>
</head>
<body>
    <header>
        <?php include_once '../vistas/template_nav.php'; ?>
    </header>
    
    <?php
        // Verificar que el archivo existe dentro de la carpeta vistas
        if (file_exists($archivoVista) && is_file($archivoVista)) {
            require $archivoVista;
        } else {
            require $rutaVistas . '/404.php';
        }
    ?>




</body>
</html>