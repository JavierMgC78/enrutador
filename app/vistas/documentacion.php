<?php include_once __DIR__ . '/template_meta.php'; ?>
<body>
    <div class="particles"></div>

    <?php include_once __DIR__ . '/template_nav.php'; ?>

    <section class="hero-section">
        <div class="container hero-content">
            <h1 class="hero-title">Documentación</h1>
            <p class="hero-subtitle">Guía técnica para desarrolladores del enrutador</p>
            <p class="hero-description">
                Esta documentación explica cómo funciona el enrutador, cómo añadir nuevas vistas, estilos y cómo mantener tu proyecto organizado. Ideal para desarrolladores que quieran entender la estructura sin complicaciones.
            </p>
            <div class="cta-buttons">
                <a href="#estructura" class="btn btn-primary">Estructura</a>
                <a href="#ejemplos" class="btn btn-secondary">Ejemplos</a>
            </div>
        </div>
    </section>

    <section class="features-section" id="estructura">
        <div class="container">
            <h2 class="section-title">Estructura del Proyecto</h2>
            <p class="section-subtitle">Archivos clave para entender el flujo</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📁</div>
                    <h3 class="feature-title">public/index.php</h3>
                    <p class="feature-description">
                        Es el punto de entrada. Procesa la URL amigable y carga la vista correspondiente:
                        <pre><code>$vista = $_GET['view'] ?? 'inicio';</code></pre>
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3 class="feature-title">core/init.php</h3>
                    <p class="feature-description">
                        Define constantes globales como rutas a carpetas y se incluye al inicio del `index.php`.
                        <pre><code>define('RUTA_VISTAS', __DIR__ . '/../app/vistas');</code></pre>
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📄</div>
                    <h3 class="feature-title">.htaccess</h3>
                    <p class="feature-description">
                        Permite usar URLs limpias como `/inicio` o `/documentacion`. Redirige todo al `index.php`.
                        <pre><code>RewriteRule ^(.*)$ public/index.php [QSA,L]</code></pre>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="ejemplos">
        <div class="container">
            <h2 class="section-title">Ejemplos Comunes</h2>
            <p class="section-subtitle">Casos típicos para expandir tu enrutador</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">➕</div>
                    <h3 class="feature-title">Crear nueva vista</h3>
                    <p class="feature-description">
                        Crea un archivo como `contacto.php` dentro de `vistas/`, y accede con la URL:<br>
                        <code>enrutador.test/contacto</code>
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3 class="feature-title">Añadir nuevo CSS</h3>
                    <p class="feature-description">
                        Define la constante `ROUTE_STYLES` y carga el CSS desde el archivo `template_meta.php`:
                        <pre><code>&lt;link rel="stylesheet" href="&lt;?= ROUTE_STYLES . '/contacto.css' ?&gt;"&gt;</code></pre>
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">Lista blanca de rutas</h3>
                    <p class="feature-description">
                        Puedes crear un array como:
                        <pre><code>$permitidas = ['inicio', 'nosotros', 'documentacion'];</code></pre>
                        Y verificar si `$vista` está en él antes de incluir la vista.
                    </p>
                </div>
            </div>
        </div>
    </section>

</body>