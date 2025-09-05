<?php
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