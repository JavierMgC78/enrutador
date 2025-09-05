<?php

// ===== RUTAS (filesystem)
define('ROUTE_ROOT', realpath(__DIR__ . '/../../'));
define('ROUTE_APP',  ROUTE_ROOT . '/app');
define('ROUTE_CORE', ROUTE_APP  . '/core');
define('ROUTE_VIEWS', ROUTE_APP . '/views');
define('ROUTE_MODELS', ROUTE_APP . '/models');
define('ROUTE_CONTROLLERS', ROUTE_APP . '/controllers');
define('SESSION_COOKIE_NAME', 'sessionCookie');

// ===== URLS (públicas)
// Detecta protocolo y host automáticamente
$esHttps    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') == 443);
$host       = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
$basePath   = rtrim(str_replace('\\','/', dirname($scriptName)), '/'); // p.ej. /enrutador/public
if ($basePath === '') { $basePath = '/'; }

define('URL_BASE',  ($esHttps ? 'https://' : 'http://') . $host . $basePath . '/');
define('URL_ASSETS', URL_BASE . 'assets/');
define('URL_CSS',    URL_ASSETS . 'css/');
define('URL_JS',     URL_ASSETS . 'js/');

// ===== CARGAS CRÍTICAS
require_once ROUTE_MODELS . '/ConecctionBD.php';
require_once ROUTE_CONTROLLERS . '/Route_controller.php';
require_once ROUTE_CONTROLLERS . '/ViewsController.php';
require_once ROUTE_CONTROLLERS . '/PermissionsController.php';
require_once ROUTE_CORE . '/Functions.php';


// incluir archivos de conexion
include_once ROUTE_MODELS . '/conexion.php';
$instanceConnection = ConnectionBD::getInstance();
$conexion = $instanceConnection->getConnection();

// incluir clase de login
include_once ROUTE_CONTROLLERS . '/Login.php';

// incluir e instanciar CookieController (la istancia siempre son las letras mayusculas del nombre de la clase)
include_once ROUTE_CONTROLLERS . '/CookieController.php';
$cc = new CookieController(SESSION_COOKIE_NAME);

// incluir clase de ViewController
include_once ROUTE_CONTROLLERS . '/ViewsController.php';
$vc = new ViewsController($conexion);

// incluir clase de PermissionsController
include_once ROUTE_CONTROLLERS . '/PermissionsController.php';
$pc = new PermissionsController();

// incluir clase de FrontController
include_once ROUTE_CONTROLLERS . '/FrontController.php';
$fc = new FrontController($cc, $vc, $pc);

/* ========================
    CONSTANTES DE RUTAS
========================= */
/*
define('RUTA_RAIZ', realpath(__DIR__ . '/../'));
define('RUTA_VISTAS', RUTA_RAIZ . '/app/vistas');
define('ROUTE_CORE', RUTA_RAIZ . '/app/core');
define('ROUTE_STYLES', RUTA_RAIZ . '/app/public/assets/css');
define('ROUTE_STYLE', 'http://enrutador.test/public/assets/css/');
define('ROUTE_MODELS', RUTA_RAIZ . '/app/modelos');
define('ROUTE_CONTROLLERS', RUTA_RAIZ . '/app/controladores');

// 1. Incluir archivo de conexión a base de datos
include_once ROUTE_MODELS . '/ConecctionBD.php'; // si lo tienes separado

// 2. Incluir clases del sistema necesarias
include_once ROUTE_CONTROLLERS . '/Route_controller.php';

// 3. Incluir funciones del sistema
include_once ROUTE_CORE . '/Functions.php';
*/