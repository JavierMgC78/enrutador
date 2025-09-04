<?php
class Login{

    private $conexion;
    private $userId;


    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function validateSessionFromCookie() 
    {
    
    
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
            if ($validacion === true) {
                $this->userId = $data['id']; // del SELECT * FROM usuarios_asigna
                
                // Generar un token de sesión único y seguro
                $sessionToken = hash('sha256', $this->userId . bin2hex(random_bytes(32)) . microtime(true));
                // Establecer la cookie de sesión con el token generado
                // La cookie tendrá una duración de 8 horas (28800 segundos)
                // HttpOnly para mayor seguridad    
                setcookie('session_token', $sessionToken, time() + 28800, '/', '', false, true);

                $saveToken = $this->saveSessionCookie($sessionToken);
                //if ($saveToken) {


                //}


            }


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

    public function saveSessionCookie($token)
    {
        $sql = "INSERT INTO tokens_usuario (usuario_id, token) VALUES (:usuario_id, :token)";
    
    
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':usuario_id', $this->userId);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

}