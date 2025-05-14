<?php
// Establecer el idioma por defecto
$idioma_default = 'es';

// Obtener el idioma de la URL
if (isset($_GET['lang'])) {
    $idioma = $_GET['lang'];
    // Guardar el idioma en una cookie que dura 30 días
    setcookie('idioma', $idioma, time() + (86400 * 30), '/');
}
// Si no hay idioma en la URL, intentar obtenerlo de la cookie
else if (isset($_COOKIE['idioma'])) {
    $idioma = $_COOKIE['idioma'];
}
// Si no hay cookie, usar el idioma por defecto
else {
    $idioma = $idioma_default;
}

// Validar que el idioma sea válido (solo permitir 'es' o 'en')
if (!in_array($idioma, ['es', 'en'])) {
    $idioma = $idioma_default;
}

// Textos en diferentes idiomas
$textos = [
    'es' => [
        'saludo' => 'Hola',
        'cerrar_sesion' => 'Cerrar sesión',
        'actualizar_datos' => 'Actualizar mis datos',
        'realizar_pedidos' => 'Realizar Pedidos',
        'buscar' => 'Buscar productos...',
        'no_productos' => 'No se encontraron productos.',
        'nuestros_productos' => 'Nuestros productos',
        'iniciar_sesion' => 'Iniciar Sesión',
        'registrar' => 'Regístrate',
        'idioma' => 'Idioma',
        'modo_oscuro' => 'Modo Oscuro',
        'activar_modo_oscuro' => 'Activar Modo Oscuro',
        'desactivar_modo_oscuro' => 'Desactivar Modo Oscuro',
        'ajustes' => 'Ajustes',
        'descripcion' => 'Descripción',
        'generos' => 'Géneros',
        'insertar_juego' => 'Insertar Juego',
        'plataformas' => 'Plataformas',
        'titulo' => 'Regístrate',
        'nombre' => 'Nombre',
        'correo' => 'Correo electrónico',
        'telefono' => 'Teléfono',
        'contraseña' => 'Contraseña',
        'confirmar_contraseña' => 'Confirmar contraseña',
        'registrar' => 'Registrar',
        'ya_tienes_cuenta' => '¿Ya tienes una cuenta?',
        'volver' => 'Volver',
        'no_tienes_cuenta' => '¿No tienes una cuenta?',
        'registrate' => 'Regístrate',
        'El correo no está registrado' => 'El correo no está registrado',
        'Todos los campos son requeridos' => 'Todos los campos son requeridos',
        'El teléfono no es válido' => 'El teléfono no es válido',
        'Las contraseñas deben de ser iguales' => 'Las contraseñas deben de ser iguales',
        '¡Registro exitoso!' => '¡Registro exitoso!',
        '¡Inicia sesión para empezar a usar tu cuenta!' => '¡Inicia sesión para empezar a usar tu cuenta!',
        'añadir_carrito' => 'Añadir carrito',
        'precio' => 'Precio',
        'titulo_tienda' => 'Tienda Videojuegos',
        'correo_no_registrado' => 'El correo no está registrado',
        'contraseña_incorrecta' => 'La contraseña es incorrecta',
        'videojuegos_populares' => 'Videojuegos populares',
    ],
    'en' => [
        'saludo' => 'Hello',
        'cerrar_sesion' => 'Log out',
        'actualizar_datos' => 'Update my data',
        'realizar_pedidos' => 'Place Orders',
        'buscar' => 'Search products...',
        'no_productos' => 'No products found.',
        'nuestros_productos' => 'Our products',
        'iniciar_sesion' => 'Log in',
        'registrar' => 'Register',
        'idioma' => 'Language',
        'modo_oscuro' => 'Dark Mode',
        'activar_modo_oscuro' => 'Activate Dark Mode',
        'desactivar_modo_oscuro' => 'Deactivate Dark Mode',
        'ajustes' => 'Settings',
        'descripcion' => 'Description',
        'generos' => 'Genres',
        'insertar_juego' => 'Insert Game',
        'plataformas' => 'Platforms',
        'titulo' => 'Register',
        'nombre' => 'Name',
        'correo' => 'Email',
        'telefono' => 'Phone',
        'contraseña' => 'Password',
        'confirmar_contraseña' => 'Confirm Password',
        'registrar' => 'Register',
        'ya_tienes_cuenta' => 'Already have an account?',
        'volver' => 'Back',
        'no_tienes_cuenta' => 'Don\'t have an account?',
        'registrate' => 'Register',
        'El correo no está registrado' => 'The email is not registered',
        'Todos los campos son requeridos' => 'All fields are required',
        'El teléfono no es válido' => 'The phone number is not valid',
        'Las contraseñas deben de ser iguales' => 'The passwords must be the same',
        '¡Registro exitoso!' => 'Registration successful!',
        '¡Inicia sesión para empezar a usar tu cuenta!' => 'Log in to start using your account!',
        'añadir_carrito' => 'Add to cart',
        'precio' => 'Price',
        'titulo_tienda' => 'Game Store',
        'correo_no_registrado' => 'The email is not registered',
        'contraseña_incorrecta' => 'The password is incorrect',
        'videojuegos_populares' => 'Popular Video Games',
    ],
];
?>