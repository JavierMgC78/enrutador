

 <!-- Partículas de fondo -->
    <div class="particles">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 1.5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 2.5s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 3.5s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 0.5s;"></div>
    </div>

    <!-- Navegación -->


    <!-- Sección Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">PHP Router</h1>
                <h2 class="hero-subtitle">Enrutamiento Inteligente y Elegante</h2>
                <p class="hero-description">
                    Experimenta el poder de un sistema de enrutamiento moderno y eficiente. 
                    Diseñado para desarrolladores que buscan simplicidad sin comprometer la funcionalidad.
                </p>
                <div class="cta-buttons">
                    <a href="/comenzar" class="btn btn-primary">Comenzar Ahora</a>
                    <a href="/documentacion" class="btn btn-secondary">Documentación</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Características -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Características Premium</h2>
            <p class="section-subtitle">
                Descubre las funcionalidades que hacen de nuestro router la elección perfecta para tu proyecto
            </p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">Rápido</h3>
                    <p class="feature-description">
                        Optimizado para máximo rendimiento con tiempos de respuesta increíblemente rápidos.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3 class="feature-title">Seguro</h3>
                    <p class="feature-description">
                        Implementa las mejores prácticas de seguridad para proteger tu aplicación.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">Precisión</h3>
                    <p class="feature-description">
                        Enrutamiento preciso con coincidencia exacta de patrones y parámetros.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Efecto de parallax suave en el scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                const speed = (index + 1) * 0.1;
                particle.style.transform = `translateY(${rate * speed}px)`;
            });
        });

        // Animación adicional para las cards cuando aparecen en viewport
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease-out forwards';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(card => {
            observer.observe(card);
        });
    </script>