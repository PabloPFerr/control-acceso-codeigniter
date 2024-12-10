$(document).ready(function() {
    // Activar el elemento del menú actual
    const currentPath = window.location.pathname;
    $('.sidebar .nav-link').each(function() {
        const linkPath = $(this).attr('href').replace(window.location.origin, '');
        if (currentPath.startsWith(linkPath) && linkPath !== '/') {
            $(this).closest('.nav-item').addClass('active');
        }
    });

    // Toggle sidebar en móvil
    $('.navbar-toggler').click(function() {
        $('.sidebar').toggleClass('show');
    });
});
