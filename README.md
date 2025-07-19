# enrutaedor
este proyecto es un enrutador simple hecho en PHP que permite cargar vistas dinámicas mediante el parámetro `view` en la URL. Ideal para proyectos pequeños que requieren navegación modular sin frameworks.
Las vistas deben ser archivos de php en la carpeta vista.
Este enrutador permite estructurar la aplicación de forma modular, cargando los archivos CSS y otros recursos solo una vez desde el archivo principal (public/index.php).

Gracias a esta estructura, todas las vistas dinámicas (inicio, nosotros, etc.) se insertan dentro de un mismo esqueleto HTML, evitando recargas innecesarias de hojas de estilo o scripts. Esto mejora el rendimiento y facilita el mantenimiento del código.

Ejemplo de estructura del index.php:
<!DOCTYPE html>
<html lang="es">
<head>
  <?php include "../vistas/template_meta.php"; // Aquí van los <link> y <meta> ?>
</head>
<body>
  <?php include "../vistas/template_nav.php"; // Menú de navegación ?>

  <main>
    <?php
      $vista = $_GET['view'] ?? 'inicio';
      $ruta = "../vistas/" . $vista . ".php";
      if (file_exists($ruta)) {
          include $ruta;
      } else {
          include "../vistas/404.php";
      }
    ?>
  </main>
</body>
</html>
