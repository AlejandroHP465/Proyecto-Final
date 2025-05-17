<?php
session_name("Tienda");
session_start();
include '../includes/idioma.php'; // Incluir el archivo de idioma
include '../includes/connect.php'; // Conexión a la base de datos

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "Juego no encontrado.";
    exit;
}

// Obtener información del juego
$statement = $pdo->prepare('SELECT * FROM producto WHERE producto_id = :id');
$statement->execute(['id' => $id]);
$juego = $statement->fetch(PDO::FETCH_ASSOC);

if (!$juego) {
    echo "Juego no encontrado.";
    exit;
}

// Verificar si el juego ya está en favoritos
$esFavorito = false;
if (isset($_SESSION['cliente_id'])) {
    $clienteId = $_SESSION['cliente_id'];
    $statement = $pdo->prepare('SELECT COUNT(*) FROM favoritos WHERE cliente_id = :cliente_id AND producto_id = :producto_id');
    $statement->execute(['cliente_id' => $clienteId, 'producto_id' => $id]);
    $esFavorito = $statement->fetchColumn() > 0;
}

// Obtener valoración promedio del juego
$statement = $pdo->prepare('SELECT AVG(valoracion) AS promedio FROM valoraciones WHERE producto_id = :id');
$statement->execute(['id' => $id]);
$valoracionPromedio = $statement->fetch(PDO::FETCH_ASSOC)['promedio'] ?? 0;

// Obtener resena del juego
$statement = $pdo->prepare('SELECT c.resena, cl.nombre, c.fecha FROM resena c INNER JOIN clientes cl ON c.cliente_id = cl.cliente_id WHERE c.producto_id = :id ORDER BY c.fecha DESC');
$statement->execute(['id' => $id]);
$resena = $statement->fetchAll(PDO::FETCH_ASSOC);

// Obtener plataformas del juego
$statement = $pdo->prepare('SELECT p.nombre FROM plataforma p INNER JOIN plataforma_juegos pj ON p.plataforma_id = pj.plataforma_id WHERE pj.producto_id = :id');
$statement->execute(['id' => $id]);
$plataformas = $statement->fetchAll(PDO::FETCH_ASSOC);

// Obtener géneros del juego
$statement = $pdo->prepare('SELECT g.nombre FROM genero g INNER JOIN genero_juegos gj ON g.genero_id = gj.genero_id WHERE gj.producto_id = :id');
$statement->execute(['id' => $id]);
$generos = $statement->fetchAll(PDO::FETCH_ASSOC);

// Manejar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'añadir') {
        // Inicializar el carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $carrito = &$_SESSION['carrito']; // Usar referencia para mantener sincronizado

        $productoId = $_POST['producto_id'];
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $Foto = $_POST['Foto'];

        // Verificar si el producto ya está en el carrito
        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad'] += 1; // Incrementar la cantidad
        } else {
            $carrito[$productoId] = [
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => 1,
                'Foto' => $Foto,
            ];
        }

        $mensaje = "Producto añadido al carrito.";
    }

    if ($_POST['accion'] === 'valorar' && isset($_SESSION['cliente_id'])) {
        // Verificar si el usuario ya valoró el producto
        $clienteId = $_SESSION['cliente_id'];
        $statement = $pdo->prepare('SELECT COUNT(*) FROM valoraciones WHERE producto_id = :producto_id AND cliente_id = :cliente_id');
        $statement->execute(['producto_id' => $id, 'cliente_id' => $clienteId]);
        $yaValoro = $statement->fetchColumn() > 0;

        if (!$yaValoro) {
            // Insertar valoración
            $valoracion = $_POST['valoracion'];
            $statement = $pdo->prepare('INSERT INTO valoraciones (producto_id, cliente_id, valoracion) VALUES (:producto_id, :cliente_id, :valoracion)');
            $statement->execute([
                'producto_id' => $id,
                'cliente_id' => $clienteId,
                'valoracion' => $valoracion,
            ]);
            $mensaje = "Gracias por tu valoración.";
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;

        } else {
            $mensaje = "Ya has valorado este producto.";
        }
    }

    if ($_POST['accion'] === 'comentar' && isset($_SESSION['cliente_id'])) {
        // Verificar si el usuario ya comentó el producto
        $clienteId = $_SESSION['cliente_id'];
        $statement = $pdo->prepare('SELECT COUNT(*) FROM resena WHERE producto_id = :producto_id AND cliente_id = :cliente_id');
        $statement->execute(['producto_id' => $id, 'cliente_id' => $clienteId]);
        $yaComento = $statement->fetchColumn() > 0;

        if (!$yaComento) {
            // Insertar resena
            $resena = trim($_POST['resena']);
            if (!empty($resena)) {
                $statement = $pdo->prepare('INSERT INTO resena (producto_id, cliente_id, resena) VALUES (:producto_id, :cliente_id, :resena)');
                $statement->execute([
                    'producto_id' => $id,
                    'cliente_id' => $clienteId,
                    'resena' => $resena,
                ]);
                $mensaje = "Gracias por tu resena.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } else {
                $mensaje = "El resena no puede estar vacío.";
            }
        } else {
            $mensaje = "Ya has comentado este producto.";
        }
    }

    if ($_POST['accion'] === 'eliminar' && isset($_SESSION['nombre']) && $_SESSION['nombre'] === 'root') {
        // Eliminar el juego de la base de datos
        $statement = $pdo->prepare('DELETE FROM producto WHERE producto_id = :id');
        $statement->execute(['id' => $id]);
        $mensaje = "Juego eliminado correctamente.";
        header("Location: index.php");
        exit;
    }

    if ($_POST['accion'] === 'editar' && isset($_SESSION['nombre']) && $_SESSION['nombre'] === 'root') {
        // Redirigir a una página de edición (puedes crear una página específica para editar)
        header("Location: editar_juego.php?id=" . $id);
        exit;
    }

    if ($_POST['accion'] === 'eliminar' && isset($_SESSION['cliente_id']) && $_SESSION['cliente_id'] === 1) {
        // Eliminar valoraciones relacionados
        $statement = $pdo->prepare('DELETE FROM valoraciones WHERE producto_id = :id');
        $statement->execute(['id' => $id]);

        // Eliminar resena relacionados
        $statement = $pdo->prepare('DELETE FROM resena WHERE producto_id = :id');
        $statement->execute(['id' => $id]);

        // Eliminar el juego de la base de datos
        $statement = $pdo->prepare('DELETE FROM producto WHERE producto_id = :id');
        $statement->execute(['id' => $id]);

        $mensaje = "Juego eliminado correctamente.";
        header("Location: index.php");
        exit;
    }

    if ($_POST['accion'] === 'editar' && isset($_SESSION['cliente_id']) && $_SESSION['cliente_id'] === 1) {
        // Redirigir a una página de edición
        header("Location: editar_juego.php?id=" . $id);
        exit;
    }

    if ($_POST['accion'] === 'favorito' && isset($_SESSION['cliente_id'])) {
        $clienteId = $_SESSION['cliente_id'];
        $productoId = $_POST['producto_id'];

        // Verificar si el producto ya está en favoritos
        $statement = $pdo->prepare('SELECT * FROM favoritos WHERE cliente_id = :cliente_id AND producto_id = :producto_id');
        $statement->execute(['cliente_id' => $clienteId, 'producto_id' => $productoId]);
        $favorito = $statement->fetch(PDO::FETCH_ASSOC);

        if ($favorito) {
            // Si ya está en favoritos, eliminarlo
            $statement = $pdo->prepare('DELETE FROM favoritos WHERE cliente_id = :cliente_id AND producto_id = :producto_id');
            $statement->execute(['cliente_id' => $clienteId, 'producto_id' => $productoId]);
            $mensaje = "Juego eliminado de favoritos.";
        } else {
            // Si no está en favoritos, añadirlo
            $statement = $pdo->prepare('INSERT INTO favoritos (cliente_id, producto_id) VALUES (:cliente_id, :producto_id)');
            $statement->execute(['cliente_id' => $clienteId, 'producto_id' => $productoId]);
            $mensaje = "Juego añadido a favoritos.";
        }

        // Recargar la página para actualizar el estado del botón
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($juego['nombre']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center">
    <header class="w-full bg-blue-600 text-white py-4 shadow-md">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($juego['nombre']); ?></h1>
            <div class="flex space-x-4">
                <a href="index.php" class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">
                    <?php echo $textos[$idioma]['volver'] ?? 'Volver'; ?>
                </a>
                <?php if (isset($_SESSION['cliente_id']) && $_SESSION['cliente_id'] !== 1): ?>
                    <a href="../views/carrito.php" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                        <?php echo $textos[$idioma]['ver_carrito'] ?? 'Ver carrito'; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if (isset($mensaje)): ?>
        <div id="mensaje" class="fixed top-4 right-4 bg-green-500 text-white py-2 px-4 rounded shadow-md">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('mensaje').remove();
            }, 3000);
        </script>
    <?php endif; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col md:flex-row items-center">
                <img src="<?php echo htmlspecialchars($juego['Foto']); ?>" alt="Foto de <?php echo htmlspecialchars($juego['nombre']); ?>" class="w-64 h-64 object-cover rounded-md shadow-md">
                <div class="md:ml-8 mt-4 md:mt-0">
                    <h2 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($juego['nombre']); ?></h2>
                    <p class="text-gray-600 mt-4"><?php echo htmlspecialchars($juego['descripcion']); ?></p>
                    <?php if (isset($_SESSION['cliente_id'])): ?>
                    <p class="text-xl font-semibold text-blue-600 mt-4"><?php echo $textos[$idioma]['precio'] ?> <?php echo htmlspecialchars($juego['precio']); ?>€</p>
                    <p class="text-lg mt-4"><?php echo $textos[$idioma]['Valoración_promedio'] ?> <?php echo number_format($valoracionPromedio, 1); ?>/5</p>
                    <?php endif; ?>
                    <!-- Mostrar géneros -->
                    <?php if (!empty($generos)): ?>
                    <div class="mt-4">
                        <p class="text-lg font-semibold text-gray-800">Géneros:</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <?php foreach ($generos as $genero): ?>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                    <?php 
                                    $iconos = [
                                        'Acción' => '⚔️',
                                        'Aventura' => '🗺️',
                                        'RPG' => '🎲',
                                        'Deportes' => '⚽',
                                        'Estrategia' => '🧠',
                                        'Simulación' => '🎮',
                                        'Carreras' => '🏎️',
                                        'Shooter' => '🎯'
                                    ];
                                    echo isset($iconos[$genero['nombre']]) ? $iconos[$genero['nombre']] . ' ' : '';
                                    echo htmlspecialchars($genero['nombre']); 
                                    ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!-- Mostrar plataformas -->
                    <?php if (!empty($plataformas)): ?>
                    <div class="mt-4">
                        <p class="text-lg font-semibold text-gray-800">Plataformas disponibles:</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <?php foreach ($plataformas as $plataforma): ?>
                                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm">
                                    <?php 
                                    $iconos = [
                                        'PC' => '💻',
                                        'PlayStation' => '🎮',
                                        'Xbox' => '🎮',
                                        'Nintendo Switch' => '🎮'
                                    ];
                                    echo isset($iconos[$plataforma['nombre']]) ? $iconos[$plataforma['nombre']] . ' ' : '';
                                    echo htmlspecialchars($plataforma['nombre']); 
                                    ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['cliente_id']) && $_SESSION['cliente_id'] !== 1): ?>
                        <form action="" method="POST" class="mt-4">
                            <input type="hidden" name="accion" value="añadir">
                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($juego['producto_id']); ?>">
                            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($juego['nombre']); ?>">
                            <input type="hidden" name="precio" value="<?php echo htmlspecialchars($juego['precio']); ?>">
                            <input type="hidden" name="Foto" value="<?php echo htmlspecialchars($juego['Foto']); ?>">
                            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                🛒 <?php echo $textos[$idioma]['añadir_carrito'] ; ?>
                            </button>
                        </form>
                        <?php if (isset($_SESSION['cliente_id']) && $_SESSION['cliente_id'] !== 1): ?>
                        <form action="" method="POST" class="mt-4">
                            <input type="hidden" name="accion" value="favorito">
                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($juego['producto_id']); ?>">
                            <button type="submit" class="<?php echo $esFavorito ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-white hover:bg-red-600 text-red-500 hover:text-white border border-red-500'; ?> py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                ❤️ <?php echo $esFavorito ? ($textos[$idioma]['eliminar_favorito'] ) : ($textos[$idioma]['añadir_favorito'] ); ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <!-- Opciones de administración para el usuario con cliente_id = 1 -->
        <?php if (isset($_SESSION['cliente_id']) && $_SESSION['cliente_id'] === 1): ?>
            <div class="mt-8">
                <h3 class="text-2xl font-bold text-gray-800">
                    <?php echo $textos[$idioma]['opciones_admin'] ; ?>
                </h3>
                <div class="flex space-x-4 mt-4">
                    <!-- Botón para editar -->
                    <form action="" method="POST">
                        <input type="hidden" name="accion" value="editar">
                        <button type="submit" class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2">
                            <?php echo $textos[$idioma]['editar_juego'] ; ?>
                        </button>
                    </form>
                    <!-- Botón para eliminar -->
                    <form action="" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este juego?');">
                        <input type="hidden" name="accion" value="eliminar">
                        <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <?php echo $textos[$idioma]['eliminar_juego'] ; ?>
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sección de valoración -->
        <?php if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] !== 1): ?>
        <div class="mt-8">
            <h3 class="text-2xl font-bold text-gray-800">
                <?php echo $textos[$idioma]['valora_juego'] ; ?>
            </h3>
            <?php if (isset($_SESSION['cliente_id'])): ?>
                <form action="" method="POST" class="flex items-center mt-4">
                    <input type="hidden" name="accion" value="valorar">
                    <div class="flex space-x-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="valoracion" value="<?php echo $i; ?>" class="hidden" onclick="rellenarEstrellas(<?php echo $i; ?>)">
                                <svg class="w-8 h-8 estrella text-gray-400 hover:text-yellow-500 cursor-pointer" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.01 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"></path>
                                </svg>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="ml-4 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <?php echo $textos[$idioma]['enviar_valoracion'] ; ?>
                    </button>
                </form>
            <?php else: ?>
                <p class="text-gray-600 mt-4">
                    <?php echo $textos[$idioma]['inicia_valorar'] ; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Sección de reseñas -->
        <?php if (isset($_SESSION['cliente_id'])): ?>
        <div class="mt-8">
            <h3 class="text-2xl font-bold text-gray-800">
                <?php echo $textos[$idioma]['resenas'] ; ?>
            </h3>
            <div class="bg-gray-50 rounded-lg shadow-md p-4 mt-4">
                <?php if (empty($resena)): ?>
                    <p class="text-gray-600 italic">
                        <?php echo $textos[$idioma]['no_resenas'] ; ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($resena as $resena): ?>
                        <div class="mb-4">
                            <p class="text-sm text-gray-800"><strong><?php echo htmlspecialchars($resena['nombre']); ?></strong> (<?php echo $resena['fecha']; ?>)</p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($resena['resena']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulario para agregar un resena -->
        <?php if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] !== 1): ?>
        <div class="mt-8">
            <h3 class="text-2xl font-bold text-gray-800">
                <?php echo $textos[$idioma]['deja_resena'] ; ?>
            </h3>
            <?php if (isset($_SESSION['cliente_id'])): ?>
                <form action="" method="POST" class="bg-white rounded-lg shadow-md p-6 mt-4">
                    <input type="hidden" name="accion" value="comentar">
                    <div class="mb-4">
                        <label for="resena" class="block text-sm font-medium text-gray-700">
                            <?php echo $textos[$idioma]['resena'] ; ?>
                        </label>
                        <textarea id="resena" name="resena" rows="4" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <?php echo $textos[$idioma]['enviar_resena'] ; ?>
                    </button>
                </form>
            <?php else: ?>
                <p class="text-gray-600 mt-4">
                    <?php echo $textos[$idioma]['inicia_resena'] ; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>

    <footer class="w-full bg-gray-800 text-white py-4 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 Tienda de Videojuegos. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>