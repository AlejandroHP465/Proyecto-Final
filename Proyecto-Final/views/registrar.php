<?php
session_name("Tienda");
session_start();

include '../includes/idioma.php';

// Obtener los valores antiguos si existen
$old = $_SESSION['old_input'] ?? [];
$errores = $_SESSION['errores'] ?? [];

// Limpiar los datos de la sesión después de obtenerlos
unset($_SESSION['old_input']);
unset($_SESSION['errores']);
session_write_close();
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $textos[$idioma]['titulo']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 <?php echo isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true' ? 'dark-mode' : ''; ?>" id="body">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-200 mb-6"><?php echo $textos[$idioma]['titulo']; ?></h1>

        <!-- Mostrar errores si existen -->
        <?php if (!empty($errores)): ?>
            <div class="mb-4">
                <?php foreach ($errores as $error): ?>
                    <div class="bg-red-500 text-white p-2 rounded mb-2">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de registro -->
        <form action="../controllers/usuarios_controller.php" method="POST" class="space-y-6">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo $textos[$idioma]['nombre']; ?>:</label>
                <input type="text" id="nombre" name="nombre" 
                    value="<?php echo htmlspecialchars($old['nombre'] ?? ''); ?>"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    aria-label="Nombre completo" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo $textos[$idioma]['correo']; ?>:</label>
                <input type="email" id="email" name="email" 
                    value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    aria-label="Correo electrónico" required>
            </div>
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo $textos[$idioma]['telefono']; ?>:</label>
                <input type="text" id="telefono" name="telefono" pattern="[0-9]{9}"
                    value="<?php echo htmlspecialchars($old['telefono'] ?? ''); ?>"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    aria-label="Número de teléfono" placeholder="123456789" required>
            </div>
            <div>
                <label for="contraseña" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo $textos[$idioma]['contraseña']; ?>:</label>
                <input type="password" id="contraseña" name="contraseña" minlength="6"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    aria-label="Contraseña" required>
            </div>
            <div>
                <label for="confirmar_contraseña" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo $textos[$idioma]['confirmar_contraseña']; ?>:</label>
                <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" minlength="6"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    aria-label="Confirmar contraseña" required>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <?php echo $textos[$idioma]['registrar']; ?>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo $textos[$idioma]['ya_tienes_cuenta']; ?></p>
            <a href="iniciar_sesion.php" class="inline-block mt-2 text-blue-600 hover:underline">
                <?php echo $textos[$idioma]['volver']; ?>
            </a>
        </div>
    </div>
</body>
</html>
