# enrutaedor
este proyecto es un enrutador simple hecho en PHP que permite cargar vistas dinámicas mediante el parámetro `view` en la URL. Ideal para proyectos pequeños que requieren navegación modular sin frameworks.
Las vistas deben ser archivos de php en la carpeta vista.
Este enrutador permite estructurar la aplicación de forma modular, cargando los archivos CSS y otros recursos solo una vez desde el archivo principal (public/index.php).

Gracias a esta estructura, todas las vistas dinámicas (inicio, nosotros, etc.) se insertan dentro de un mismo esqueleto HTML, evitando recargas innecesarias de hojas de estilo o scripts. Esto mejora el rendimiento y facilita el mantenimiento del código.

================
Journey general
================
### Puntos clave del enrutamiento:
- Todo pasa por `index.php` (enrutador tipo *Front Controller*).
- No se permiten accesos directos a vistas fuera de las definidas en la lista blanca.
- El FRS puede ser usado también para aplicar filtros como requerir login o verificar permisos.
Se lanza una **petición GET de navegación**. Esta petición está compuesta por el dominio del sitio y un último segmento que indica la vista solicitada.

Ejemplo:
http://enrutador.test/inicio

Donde:
- `http://enrutador.test/` es el **dominio**.
- `inicio` es la **vista** o página que debe cargarse.

A este último segmento de la URL lo llamaremos **FRS** (*Final Route Segment*).

Para asegurar un correcto funcionamiento, se define un único punto de acceso a la aplicación: `index.php`, ubicado en la carpeta `/public` del proyecto.

===========================
INDEX.PHP (punto de acceso)
===========================

Al cargarse `index.php`, se ejecutan los siguientes pasos:

1. **Conexión e inicialización**: Se importa un archivo `init.php` donde se configuran rutas, constantes globales y la conexión a base de datos.
2. **Cargar whitelist**: Se realiza una consulta a la base de datos (o se carga un array estático) con la lista de vistas permitidas por el sistema.
3. **Extraer la ruta solicitada**: A través de `$_SERVER['REQUEST_URI']` se obtiene la URL solicitada y se extrae el FRS.
4. **Comparar FRS con lista blanca**: Si el FRS está presente en la lista de vistas permitidas, se incluye dinámicamente (`include`) el archivo PHP correspondiente dentro de `/app/vistas`.
5. **Vista no permitida o inexistente**: Si el FRS no se encuentra en la lista blanca, se carga una vista de error (`404.php`).
6. **Vista restringida**: Si la vista solicitada requiere autenticación (es decir, solo puede ser accedida por usuarios autenticados), pero el usuario no tiene una cookie válida de acceso(que se genera posterior a su autenticación), entonces:

No se carga la vista solicitada. En su lugar, se redirige o carga una vista especial que indica:
👉 “Acceso no permitido. Necesita iniciar sesión primero.”

Esta validación se realiza antes de incluir la vista, usando el valor requiere_login dentro de la lista blanca o configuración de rutas.
---

==========================
LISTA BLANCA DE VISTAS
==========================

El sistema utiliza una **lista blanca** (whiteList) de vistas permitidas. Esta lista puede estar definida como:

- Un arreglo en PHP (`$this->whiteList`), o
- Una tabla en base de datos (`whiteList`).

Cada entrada representa una ruta válida y puede tener campos adicionales que controlan su comportamiento.

Ejemplo de estructura básica (en forma de array PHP):

```php
[
  [
      'ruta' => 'inicio',
      'requiere_login' => 0
  ],
  [
      'ruta' => 'dashboard',
      'requiere_login' => 1
  ],
  [
      'ruta' => 'perfil',
      'requiere_login' => 1
  ]
]









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
