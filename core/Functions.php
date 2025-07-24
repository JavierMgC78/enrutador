<?php
include_once '../Coneccion/ConecctionBD.php';

class Functions
{

private $connection;
private $connectionInstance;

public function __construct() {
    $this->connectionInstance = ConnectionBD::getInstance();
    $this->connection = $this->connectionInstance->getConnection();
}

public function whiteList(){
    $sql = "SELECT ruta FROM rutas_permitidas WHERE activo = 1";
    $stmt = $this->connection->prepare($sql);    
    $stmt->execute();
    $rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rutas;
}


}