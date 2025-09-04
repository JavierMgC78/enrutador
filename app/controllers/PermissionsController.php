<?php
// PermissionsController.php
class PermissionsController
{
    private PDO $conecction;
    private CookieController $cookie;
    private ?string $vista = null;       // vista resultante (login, 403 o la solicitada)
    private string $loginView = 'login'; // por si usas otro nombre
    private bool $useDbValidation;       // ON = valida token/roles en BD
    private array $viewMetaData; // metadata de la vista solicitada
    
    public function __construct()
    {
        //$this->db = $db;
        //$this->cookie = $cookie;
        //$this->useDbValidation = $useDbValidation;
    }


}