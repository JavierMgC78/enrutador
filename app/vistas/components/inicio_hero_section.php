<?php
function hero_section($opciones = []) {
    $defaults = [
        'background_color' => '#225CE5',
        'background_image' => '',
        'overlay' => true,
        'nombre_negocio' => 'Nombre del Negocio',
        'categoria' => 'Categoría',
        'ubicacion' => 'Ubicación',
        'cta_texto' => 'Ver más',
        'cta_enlace' => '#',
        'cta_clase' => 'btn-hero-primary'
    ];

    $config = array_merge($defaults, $opciones);

    $estilo_fondo = $config['background_image'] !== ''
        ? "background-image: url('/public/assets/img/{$config['background_image']}'); background-size: cover; background-position: center;"
        : "background-color: {$config['background_color']};";

    $overlay_style = $config['overlay'] ? "background-color: rgba(0,0,0,0.5);" : '';

    echo '<section class="hero-section" style="' . $estilo_fondo . '">';
    echo '<div class="hero-overlay" style="' . $overlay_style . '">';
    echo '<div class="hero-content">';
    echo '<h1 class="hero-title">' . $config['nombre_negocio'] . '</h1>';
    echo '<p class="hero-subtitle">' . $config['categoria'] . ' · ' . $config['ubicacion'] . '</p>';
    echo '<a href="' . $config['cta_enlace'] . '" class="' . $config['cta_clase'] . '">' . $config['cta_texto'] . '</a>';
    echo '</div>'; // .hero-content
    echo '</div>'; // .hero-overlay
    echo '</section>';
}