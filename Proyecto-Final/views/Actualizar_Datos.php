<?php
session_name("Tienda");
session_start();
include '../includes/idioma.php'; // Incluir el archivo de idioma
include '../includes/connect.php'; // Conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['cliente_id'])) {
    header('Location: iniciar_sesion.php');
    exit;
}

$mensaje_exito = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errores = [];

    $nombre = htmlspecialchars(trim($_POST["nombre"] ?? ''));
    $email = htmlspecialchars(trim($_POST["email"] ?? ''));
    $telefono = htmlspecialchars(trim($_POST["telefono"] ?? ''));
    $contraseña = $_POST["contraseña"] ?? '';
    $confirmarContraseña = $_POST["confirmar_contraseña"] ?? '';

    // Validaciones
    if (!empty($contraseña) && $contraseña !== $confirmarContraseña) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    if (!empty($telefono) && !preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }

    if (empty($errores)) {
        try {
            // Construir la consulta SQL dinámicamente
            $campos = [];
            $valores = [':cliente_id' => $_SESSION['cliente_id']];

            if (!empty($nombre)) {
                $campos[] = "nombre = :nombre";
                $valores[':nombre'] = $nombre;
            }

            if (!empty($email)) {
                $campos[] = "email = :email";
                $valores[':email'] = $email;
            }

            if (!empty($telefono)) {
                $campos[] = "telefono = :telefono";
                $valores[':telefono'] = $telefono;
            }

            if (!empty($contraseña)) {
                $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);
                $campos[] = "contrasena = :contrasena";
                $valores[':contrasena'] = $hashedPassword;
            }

            // Solo ejecutar la consulta si hay campos para actualizar
            if (!empty($campos)) {
                $query = "UPDATE clientes SET " . implode(", ", $campos) . " WHERE cliente_id = :cliente_id";
                $statement = $pdo->prepare($query);
                $statement->execute($valores);

                // Actualizar los datos en la sesión si se modificaron
                if (!empty($nombre)) {
                    $_SESSION['usuario'] = $nombre;
                }
                if (!empty($email)) {
                    $_SESSION['email'] = $email;
                }
                if (!empty($telefono)) {
                    $_SESSION['telefono'] = $telefono;
                }

                $mensaje_exito = "¡Datos actualizados correctamente!";
            } else {
                echo '<div class="bg-yellow-100 text-yellow-700 p-4 rounded mb-4 text-center">No se realizaron cambios.</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="bg-red-100 text-red-700 p-4 rounded mb-4 text-center">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        echo '<div class="bg-red-100 text-red-700 p-4 rounded mb-4">';
        foreach ($errores as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $textos[$idioma]['actualizar_datos']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center" id="body">
    <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-md w-full max-w-lg">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200 text-center">
            <?php echo $textos[$idioma]['actualizar_datos']; ?>
        </h1>

        <!-- Formulario -->
        <form action="" method="POST" class="space-y-6">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_SESSION['telefono'] ?? ''); ?>" pattern="[0-9]{9}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="contraseña" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva Contraseña</label>
                <input type="password" id="contraseña" name="contraseña" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="confirmar_contraseña" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
                <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Actualizar Datos
            </button>

            <p class="text-center mt-4">
                <a href="index.php" class="text-blue-600 hover:underline">Volver a la página principal</a>
            </p>
        </form>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mt-6 text-center">
                <?php echo $mensaje_exito; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>