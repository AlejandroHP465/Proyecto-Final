<!-- filepath: c:\xampp\htdocs\Proyecto-Final\views\perfil_usuario.php -->
<?php
session_name("Tienda");
session_start();
include '../includes/connect.php';
include '../includes/idioma.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header('Location: iniciar_sesion.php');
    exit;
}

// Obtener la información del usuario desde la base de datos
$email = $_SESSION['email'];
$statement = $pdo->prepare('SELECT * FROM clientes WHERE email = :email');
$statement->execute(['email' => $email]);
$usuario = $statement->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra el usuario, redirigir
if (!$usuario) {
    header('Location: index.php');
    exit;
}

// Obtener mensajes de error o éxito
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $textos[$idioma]['perfil_usuario'] ; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen">
    <?php include '../includes/header.php'; ?>

    <main class="container mx-auto py-8 px-4">
        <!-- Mensajes de error -->
        <?php if ($error): ?>
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Título principal -->
        <?php if ($usuario['cliente_id'] === 1): ?>
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-gray-200 mb-8">
                <?php echo $textos[$idioma]['acciones_admin'] ; ?>
            </h1>
        <?php else: ?>
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-gray-200 mb-8">
                <?php echo $textos[$idioma]['perfil_usuario'] ; ?>
            </h1>
        <?php endif; ?>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <!-- Información personal -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    <?php echo $textos[$idioma]['informacion_personal'] ?? 'Información Personal'; ?>
                </h2>
                <p class="text-gray-700 dark:text-gray-300"><strong><?php echo $textos[$idioma]['nombre'] ; ?>:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                <p class="text-gray-700 dark:text-gray-300"><strong><?php echo $textos[$idioma]['correo'] ; ?>:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            </section>

            <?php if ($usuario['cliente_id'] !== 1): ?>
            <!-- Lista de favoritos -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    <?php echo $textos[$idioma]['favoritos'] ?? 'Lista de Favoritos'; ?>
                </h2>
                <ul class="list-disc list-inside space-y-2">
                    <?php
                    $statement = $pdo->prepare('SELECT p.nombre FROM favoritos f JOIN producto p ON f.producto_id = p.producto_id WHERE f.cliente_id = :cliente_id');
                    $statement->execute(['cliente_id' => $usuario['cliente_id']]);
                    $favoritos = $statement->fetchAll(PDO::FETCH_ASSOC);

                    if ($favoritos) {
                        foreach ($favoritos as $favorito) {
                            echo '<li class="text-gray-700 dark:text-gray-300">' . htmlspecialchars($favorito['nombre']) . '</li>';
                        }
                    } else {
                        echo '<li class="text-gray-500 dark:text-gray-400 italic">' . ($textos[$idioma]['no_favoritos'] ) . '</li>';
                    }
                    ?>
                </ul>
            </section>

            <!-- Reseñas -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    <?php echo $textos[$idioma]['resenas'] ?? 'Reseñas'; ?>
                </h2>
                <ul class="list-disc list-inside space-y-2">
                    <?php
                    $statement = $pdo->prepare('SELECT c.resena, p.nombre AS producto FROM resena c JOIN producto p ON c.producto_id = p.producto_id WHERE c.cliente_id = :cliente_id');
                    $statement->execute(['cliente_id' => $usuario['cliente_id']]);
                    $resenas = $statement->fetchAll(PDO::FETCH_ASSOC);

                    if ($resenas) {
                        foreach ($resenas as $resena) {
                            echo '<li class="text-gray-700 dark:text-gray-300"><strong>' . htmlspecialchars($resena['producto']) . ':</strong> ' . htmlspecialchars($resena['resena']) . '</li>';
                        }
                    } else {
                        echo '<li class="text-gray-500 dark:text-gray-400 italic">' . ($textos[$idioma]['no_resenas'] ) . '</li>';
                    }
                    ?>
                </ul>
            </section>
            <?php else: ?>
            <!-- Panel de Administración -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    <?php echo $textos[$idioma]['acciones_admin'] ; ?>
                </h2>
                <div class="flex flex-col space-y-4">
                    <a href="insertar_juego.php" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded text-center">
                        ➕ <?php echo $textos[$idioma]['insertar_juego'] ; ?>
                    </a>
                    <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-center">
                        🎮 <?php echo $textos[$idioma]['gestionar_juegos'] ; ?>
                    </a>
                </div>
            </section>
            <?php endif; ?>

            <!-- Botón para eliminar el usuario -->
            <?php if ($usuario['cliente_id'] !== 1): ?>
            <section class="mt-8">
                <form method="POST" action="eliminar_usuario.php" onsubmit="return confirm('<?php echo $textos[$idioma]['confirmar_eliminar'] ?? '¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer y perderás todos tus datos, incluyendo el historial de compras, reseñas y favoritos.'; ?>');">
                    <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($usuario['cliente_id']); ?>">
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded">
                        🗑️ <?php echo $textos[$idioma]['eliminar_cuenta'] ; ?>
                    </button>
                </form>
            </section>
            <?php endif; ?>

            <!-- Botón para volver atrás -->
            <div class="mt-8 text-center">
                <a href="javascript:history.back()" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    ← <?php echo $textos[$idioma]['volver'] ; ?>
                </a>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>

</html>