<?php
session_name("Tienda");
session_start();
include '../includes/idioma.php'; // Incluir el archivo de idioma
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $textos[$idioma]['iniciar_sesion']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900" id="body">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-200 mb-6">
            <?php echo $textos[$idioma]['iniciar_sesion']; ?>
        </h1>

        <!-- Mostrar errores si existen -->
        <?php if (!empty($errores)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php foreach ($errores as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de inicio de sesión -->
        <form action="" method="post" class="space-y-6">
            <div>
                <label for="correo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <?php echo $textos[$idioma]['correo']; ?>:
                </label>
                <input type="email" id="correo" name="email" 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    aria-label="Correo electrónico">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <?php echo $textos[$idioma]['contraseña']; ?>:
                </label>
                <input type="password" id="password" name="contraseña" 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    aria-label="Contraseña">
            </div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <?php echo $textos[$idioma]['iniciar_sesion']; ?>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <?php echo $textos[$idioma]['no_tienes_cuenta']; ?>
            </p>
            <a href="registrar.php"
                class="inline-block mt-2 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                <?php echo $textos[$idioma]['registrate']; ?>
            </a>
        </div>
        <div class="mt-4 text-center">
            <a href="index.php" class="text-blue-600 hover:underline">
                <?php echo $textos[$idioma]['volver']; ?>
            </a>
        </div>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $errores = [];
            $email = trim($_POST["email"] ?? '');
            $contraseña = trim($_POST["contraseña"] ?? '');

            try {
                include '../includes/connect.php';

                $query = "SELECT * FROM clientes WHERE email = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$email]);

                if ($stmt->rowCount() > 0) {
                    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!password_verify($contraseña, $cliente['contrasena'])) {
                        $errores[] = $textos[$idioma]['contraseña_incorrecta'];
                    }
                } else {
                    $errores[] = $textos[$idioma]['correo_no_registrado'];
                }

                if (empty($errores)) {
                    $_SESSION['cliente_id'] = $cliente['cliente_id'];
                    $_SESSION['email'] = $cliente['email'];
                    $_SESSION['usuario'] = $cliente['nombre'];

                    // Cargar el carrito del usuario desde la base de datos
                    $statement = $pdo->prepare('SELECT producto_id, cantidad FROM carrito WHERE cliente_id = :cliente_id');
                    $statement->execute(['cliente_id' => $cliente['cliente_id']]);
                    $carrito = $statement->fetchAll(PDO::FETCH_ASSOC);

                    // Inicializar el carrito en la sesión
                    $_SESSION['carrito'] = [];
                    foreach ($carrito as $item) {
                        $_SESSION['carrito'][$item['producto_id']] = [
                            'cantidad' => $item['cantidad']
                        ];
                    }

                    header("Location: index.php");
                    exit();
                } else {
                    echo '<div class="mt-4 text-red-600">';
                    foreach ($errores as $error) {
                        echo "<p>$error</p>";
                    }
                    echo '</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="mt-4 text-red-600">';
                echo "Error: " . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
        }
        ?>
    </div>


</body>

</html>