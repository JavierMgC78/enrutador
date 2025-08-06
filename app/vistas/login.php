<section class="hero-section">
    <div class="container hero-content">
        <h1 class="hero-title">Acceder</h1>
        <p class="hero-subtitle">Introduce tus datos para ingresar</p>

        <form action="/procesar_login.php" method="POST" style="max-width: 400px; margin: 0 auto; background: rgba(255,255,255,0.05); padding: 40px; border-radius: 20px; backdrop-filter: blur(10px); box-shadow: var(--shadow-soft); border: 1px solid rgba(212,175,55,0.2);">
            <div style="margin-bottom: 20px;">
                <label for="usuario" style="display:block; margin-bottom: 5px; color: var(--text-secondary); font-weight: 600;">Usuario</label>
                <input type="text" id="usuario" name="usuario" required style="width: 100%; padding: 12px 15px; border-radius: 10px; border: none; background: #1a1a1a; color: var(--text-primary); outline: none;">
            </div>

            <div style="margin-bottom: 30px;">
                <label for="password" style="display:block; margin-bottom: 5px; color: var(--text-secondary); font-weight: 600;">Contraseña</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 12px 15px; border-radius: 10px; border: none; background: #1a1a1a; color: var(--text-primary); outline: none;">
            </div>

            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>
        </form>
    </div>
</section>
