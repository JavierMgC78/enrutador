<?php
declare(strict_types=1);

class FrontController
{
    private ViewsController $vc;
    private CookieController $cc;
    private PermissionsController $pc;
    private PDO $conexion;
    private string $session_token;
    private string $view;
    private array $viewMetaData;

    public function __construct(CookieController $cc, ViewsController $vc, PermissionsController $pc)     
    {
        $this->vc = $vc;
        $this->cc = $cc;
        $this->pc = $pc;
        //valor prueba
        $this->view = 'inicio';
    }


    /**
     * Punto de entrada del router por request.
     * Pipeline: extract -> sanitize -> assign (y aquí puedes añadir políticas de acceso).
     */
    public function handler(?string $requestUri = null): void
    {   
      $this->vc->handler($requestUri);
        




   

       
        // 4) (Opcional) Reglas de acceso: si requiere login y no está logueado -> LOGIN_VIEW
        /*if ($this->inWhiteList
            && isset($this->currentViewMeta['require_login'])
            && (int)$this->currentViewMeta['require_login'] === 1
            && !$this->isLoggedIn()) {
            $this->view = self::LOGIN_VIEW;
        }*/
    }

    public function getView(): string
    {
        return $this->view;
    }

    /*public function getCurrentViewMetaData(): array
    {
        return $this->viewMetaData;
    }*/
}