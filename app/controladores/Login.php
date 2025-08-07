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
        

        $validacion = password_verify($contrasena, $data['contrasena']);
        //return $validacion;


        if ($validacion) {
            setcookie('usuario', $data['usuario'], time() + 28800, '/', domain: '', secure: false, httponly: true); // 8 horas, HttpOnly
            
            //header('Location: /app/vistas/dashboard.php');
            return [
                'status' => true,
                'vista' => 'dashboard'];
           
        } else {
            return [
                'status' => false,
                'vista' => 'index'];
        }
    }
}