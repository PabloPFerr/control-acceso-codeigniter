document.addEventListener('DOMContentLoaded', function() {
    const backgrounds = [
        'mecanica-bg.jpg',
        'mecanica-bg-2.jpg',
        'mecanica-bg-3.jpg',
        'mecanica-bg-4.jpg',
        'mecanica-bg-5.jpg'
    ];
    
    // Seleccionar una imagen aleatoria
    const randomBg = backgrounds[Math.floor(Math.random() * backgrounds.length)];
    
    // Construir la URL base
    const baseUrl = document.querySelector('base')?.href || window.location.origin;
    
    // Aplicar la imagen como fondo con la ruta completa
    document.body.style.background = `linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('${baseUrl}/assets/img/${randomBg}') no-repeat center center fixed`;
    document.body.style.backgroundSize = 'cover';
    
    // Debug
    console.log('Imagen de fondo seleccionada:', `${baseUrl}/assets/img/${randomBg}`);
});
