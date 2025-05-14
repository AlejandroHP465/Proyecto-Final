<?php
session_name("Tienda");
session_start();
include '../includes/connect.php'; // Conexión a la base de datos
include '../includes/idioma.php'; // Archivo de idioma
include '../models/Producto.php'; // Modelo de productos

// Cerrar sesión
if (isset($_POST["cerrar"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Validación de seguridad para el término de búsqueda
$searchQuery = isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : '';

// Obtener productos desde el modelo con manejo de errores
try {
    $productos = Producto::buscarProductos($pdo, $searchQuery);
} catch (PDOException $e) {
    error_log("Error al buscar productos: " . $e->getMessage());
    $productos = [];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $textos[$idioma]['titulo_tienda']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100" id="body">
    <script src="../assets/js/scripts.js"></script>
    <?php include '../includes/header.php'; ?>

    <!-- Contenedor para notificaciones -->
    <div id="notification-container" class="fixed top-4 right-4 z-50"></div>

    <main class="container mx-auto py-8 px-4">
        <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-10"><?php echo $textos[$idioma]['nuestros_productos']; ?></h2>

        <!-- Barra de búsqueda -->
        <form method="GET" action="index.php" class="mb-10" role="search">
            <div class="flex items-center justify-center">
                <input type="text" 
                       name="search" 
                       placeholder="<?php echo $textos[$idioma]['buscar']; ?>" 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       aria-label="<?php echo $textos[$idioma]['buscar']; ?>"
                       class="w-full max-w-lg px-4 py-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700"
                        aria-label="<?php echo $textos[$idioma]['buscar']; ?>">
                    <?php echo $textos[$idioma]['buscar']; ?>
                </button>
            </div>
        </form>

        <!-- Productos -->
        <section id="productList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($productos)): ?>
                <p class="text-center text-gray-600"><?php echo $textos[$idioma]['no_productos']; ?></p>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="product bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <a href="../views/juego_detalle.php?id=<?php echo htmlspecialchars($producto->getId()); ?>" 
                           aria-label="<?php echo htmlspecialchars($producto->getNombre()); ?>">
                            <img src="<?php echo htmlspecialchars($producto->getFoto()); ?>" 
                                 alt="<?php echo htmlspecialchars($producto->getNombre()); ?>"
                                 class="w-full h-48 object-cover"
                                 loading="lazy"
                                 width="300"
                                 height="192">
                        </a>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($producto->getNombre()); ?></h3>
                            <p class="text-blue-500 font-semibold mt-2"><?php echo htmlspecialchars($producto->getPrecio()); ?>€</p>
                            <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($producto->getDescripcion()); ?></p>
                            <div class="mt-4 flex justify-between items-center">
                                <!-- Botón de añadir a favoritos -->
                                <?php if (isset($_SESSION['cliente_id']) && $_SESSION['cliente_id'] !== 1): ?>
                                <form method="POST" action="favoritos.php" class="inline">
                                    <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto->getId()); ?>">
                                    <button type="submit" 
                                            class="bg-white hover:bg-red-600 text-red-500 hover:text-white py-1 px-3 rounded border border-red-500"
                                            aria-label="<?php echo $textos[$idioma]['añadir_favoritos'] ?? 'Añadir a favoritos'; ?>">
                                        ❤️
                                    </button>
                                </form>
                                <?php endif; ?>
                                <!-- Botón de añadir al carrito -->
                                <?php if (isset($_SESSION['email']) && $_SESSION['cliente_id'] !== 1): ?>
                                    <form onsubmit="handleAddToCart(event, '<?php echo htmlspecialchars($producto->getNombre()); ?>', <?php echo htmlspecialchars($producto->getId()); ?>)">
                                        <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto->getId()); ?>">
                                        <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        🛒 <?php echo $textos[$idioma]['añadir_carrito']; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Paginación -->
        <div id="paginacion" class="flex justify-center gap-2 mt-8"></div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>

</html>