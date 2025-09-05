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
        // 1) Extraer FRS tal como viene en la URL
        $frs = $this->vc->extractFrs();

        // 2) Sanitizar para obtener el slug que usará la comparación
        $sanitizedFrs = $this->vc->sanitizeFrs($frs);

        // 3) Asignar vista (única fuente de verdad para $this->view)
        $this->vc->assignView($sanitizedFrs);

        $this->view = $this->vc->getView();
    }


    /**
     * Punto de entrada del router por request.
     * Pipeline: extract -> sanitize -> assign (y aquí puedes añadir políticas de acceso).
     */
    public function handler(?string $requestUri = null): void
    {   
      //$this->vc->handler($requestUri);
      //$this->view = $this->vc->getView();  
        
      

       
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