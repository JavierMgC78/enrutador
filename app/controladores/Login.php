<?php


class Login{

    private $conexion;


    public function __construct($conexion) {
        $this->conexion = $conexion;

    }

    public function authenticate($user, $contrasena)
    {
        $Sql = "SELECT * FROM usuarios_asigna WHERE usuario = :usuario";
        $stmt = $this->conexion->prepare($Sql);
        $stmt->bindParam(':usuario', $user);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        //return $data;

        if (password_verify($contrasena, $data['contrasena'])) {
            setcookie('usuario', $user['usuario'], time() + 28800, '/', '', false, true); // 8 horas, HttpOnly
            
            //header('Location: /app/vistas/dashboard.php');
            
            return true;
        } else {
            return false;
        }
    }
}