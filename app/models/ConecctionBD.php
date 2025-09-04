<?php
/* * ConnectionBD.php
 * Clase para manejar la conexión a la base de datos usando PDO.
 * Implementa el patrón Singleton para asegurar una única instancia de conexión.
 * 
 * * Uso:
 * 1)Se crea la instancia de la conexion
 * $conectionInstance = ConnectionBD::getInstance()
 * 
 * 2) Para obtener la conexión PDO, se llama al método getConnection():
 * $connection = $conectionInstance->getConnection();
 */
class ConnectionBD {
    // Configuración de la conexión (deberías mover esto a un archivo de configuración)
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'enrutador';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_CHARSET = 'utf8mb4';

    // Instancia única (para implementar patrón Singleton)
    private static $instance = null;
    
    // Conexión PDO
    private $connection;

    // Constructor privado para implementar Singleton
    private function __construct() {
        $this->connect();
    }



    /**
     * Método para obtener la instancia única (Singleton)
     * 
     * @return ConnectionBD
     */
    
    public static function getInstance(): ConnectionBD {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }



    /**
     * Establece la conexión PDO con la base de datos
     * 
     * @return void
     * @throws PDOException Si la conexión falla
     */
    private function connect(): void {
        try {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
        } catch (PDOException $e) {
            // Log del error (en producción usa un sistema de logging)
            error_log("Error de conexión a BD: " . $e->getMessage());
            
            // Mensaje genérico para el usuario (no reveles detalles internos)
            throw new PDOException("Error al conectar con la base de datos. Por favor, inténtelo más tarde.");
        }
    }


    
    /**
     * Devuelve la conexión PDO
     *
     * @return PDO
     **/
    public function getConnection(): PDO {
        // Verifica si la conexión sigue activa
        if ($this->connection === null) {
            $this->connect();
        }
        
        return $this->connection;
    }

    /**
     * Evita la clonación del objeto (Singleton)
     */
    private function __clone() {}
    
    /**
     * Evita la deserialización del objeto (Singleton)
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar una conexión a BD");
    }
    
    /**
     * Cierra la conexión cuando el objeto es destruido
     */
    public function __destruct() {
        $this->connection = null;
    }
}