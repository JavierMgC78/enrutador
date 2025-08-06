<?php


class Functions
{

    private $connection;
    private $connectionInstance;

    public function __construct($connection) {
        
        $this->connection = $connection;
    }

    public function whiteList(){
        $sql = "SELECT ruta, requiere_login FROM rutas_permitidas WHERE activo = 1";
        $stmt = $this->connection->prepare($sql);    
        $stmt->execute();
        $rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rutas;
    }

}