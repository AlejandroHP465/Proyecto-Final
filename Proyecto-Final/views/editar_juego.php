<?php
session_name("Tienda");
session_start();
include '../includes/idioma.php'; // Incluir el archivo de idioma
include '../includes/connect.php'; // Conexión a la base de datos

// Verificar si el usuario tiene permisos (cliente_id = 1)
if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] !== 1) {
    header("Location: index.php");
    exit;
}

// Obtener el ID del juego desde la URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Obtener los datos actuales del juego
$statement = $pdo->prepare('SELECT * FROM producto WHERE producto_id = :id');
$statement->execute(['id' => $id]);
$juego = $statement->fetch(PDO::FETCH_ASSOC);

if (!$juego) {
    header("Location: index.php");
    exit;
}

// Obtener géneros actuales del juego
$stmt = $pdo->prepare('SELECT genero_id FROM genero_juegos WHERE producto_id = :id');
$stmt->execute(['id' => $id]);
$generos_actuales = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Obtener plataformas actuales del juego
$stmt = $pdo->prepare('SELECT plataforma_id FROM plataforma_juegos WHERE producto_id = :id');
$stmt->execute(['id' => $id]);
$plataformas_actuales = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Obtener todos los géneros disponibles
$stmt = $pdo->query('SELECT * FROM genero ORDER BY nombre');
$generos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las plataformas disponibles
$stmt = $pdo->query('SELECT * FROM plataforma ORDER BY nombre');
$plataformas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejar la actualización del juego
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];
    
    // Construir array de campos a actualizar
    $campos = [];
    $params = ['id' => $id];

    // Validar y procesar nombre
    if (isset($_POST['nombre']) && trim($_POST['nombre']) !== '') {
        $params['nombre'] = trim($_POST['nombre']);
        $campos[] = "nombre = :nombre";
    }

    // Validar y procesar descripción
    if (isset($_POST['descripcion']) && trim($_POST['descripcion']) !== '') {
        $params['descripcion'] = trim($_POST['descripcion']);
        $campos[] = "descripcion = :descripcion";
    }

    // Validar y procesar precio
    if (isset($_POST['precio']) && is_numeric($_POST['precio']) && $_POST['precio'] > 0) {
        $params['precio'] = floatval($_POST['precio']);
        $campos[] = "precio = :precio";
    }

    // Procesar foto solo si se ha subido una nueva
    if (isset($_FILES['foto']) && $_FILES['foto']['size'] > 0) {
        $foto = $_FILES['foto'];
        
        // Validar la imagen
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if ($foto['size'] > $maxSize) {
            $errores[] = "La imagen no debe superar los 5MB.";
        } elseif (!in_array($foto['type'], $tiposPermitidos)) {
            $errores[] = "Solo se permiten imágenes JPG, PNG o WEBP.";
        } else {
            $fotoNombre = uniqid() . '-' . basename($foto['name']);
            $fotoRuta = '../assets/img/' . $fotoNombre;

            // Verificar que el directorio exista
            if (!is_dir('../assets/img/')) {
                mkdir('../assets/img/', 0755, true);
            }

            // Intentar mover el archivo
            if (move_uploaded_file($foto['tmp_name'], $fotoRuta)) {
                $params['foto'] = $fotoRuta;
                $campos[] = "foto = :foto";
                
                // Eliminar la foto anterior si existe
                if (!empty($juego['foto']) && file_exists($juego['foto']) && $juego['foto'] !== $fotoRuta) {
                    unlink($juego['foto']);
                }
            } else {
                $errores[] = "Error al guardar la imagen. Verifica los permisos del directorio.";
            }
        }
    }

    // Si hay campos para actualizar y no hay errores
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            // Actualizar datos básicos del juego si hay campos para actualizar
            if (!empty($campos)) {
                $sql = 'UPDATE producto SET ' . implode(', ', $campos) . ' WHERE producto_id = :id';
                $statement = $pdo->prepare($sql);
                $statement->execute($params);
            }

            // Actualizar géneros
            $pdo->prepare('DELETE FROM genero_juegos WHERE producto_id = :producto_id')
                ->execute(['producto_id' => $id]);
            
            if (isset($_POST['generos']) && is_array($_POST['generos'])) {
                $stmt = $pdo->prepare('INSERT INTO genero_juegos (genero_id, producto_id) VALUES (:genero_id, :producto_id)');
                foreach ($_POST['generos'] as $genero_id) {
                    $stmt->execute([
                        'genero_id' => $genero_id,
                        'producto_id' => $id
                    ]);
                }
            }

            // Actualizar plataformas
            $pdo->prepare('DELETE FROM plataforma_juegos WHERE producto_id = :producto_id')
                ->execute(['producto_id' => $id]);
            
            if (isset($_POST['plataformas']) && is_array($_POST['plataformas'])) {
                $stmt = $pdo->prepare('INSERT INTO plataforma_juegos (plataforma_id, producto_id) VALUES (:plataforma_id, :producto_id)');
                foreach ($_POST['plataformas'] as $plataforma_id) {
                    $stmt->execute([
                        'plataforma_id' => $plataforma_id,
                        'producto_id' => $id
                    ]);
                }
            }

            $pdo->commit();
            $_SESSION['mensaje'] = "Juego actualizado correctamente.";
            header("Location: juego_detalle.php?id=" . $id);
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errores[] = "Error al actualizar el juego: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Juego</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-md w-full max-w-lg">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Editar Juego</h1>

        <!-- Mostrar errores -->
        <?php if (!empty($errores)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del juego</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($juego['nombre']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"><?php echo htmlspecialchars($juego['descripcion']); ?></textarea>
            </div>

            <div>
                <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio</label>
                <input type="number" step="0.01" id="precio" name="precio" value="<?php echo htmlspecialchars($juego['precio']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="generos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Géneros</label>
                <select id="generos" name="generos[]" multiple class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?php echo $genero['genero_id']; ?>" 
                                <?php echo in_array($genero['genero_id'], $generos_actuales) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genero['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-sm text-gray-500 mt-1">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples géneros.</p>
            </div>

            <div>
                <label for="plataformas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plataformas</label>
                <select id="plataformas" name="plataformas[]" multiple class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                    <?php foreach ($plataformas as $plataforma): ?>
                        <option value="<?php echo $plataforma['plataforma_id']; ?>" 
                                <?php echo in_array($plataforma['plataforma_id'], $plataformas_actuales) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($plataforma['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-sm text-gray-500 mt-1">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples plataformas.</p>
            </div>

            <div>
                <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen actual</label>
                <?php if (!empty($juego['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($juego['foto']); ?>" alt="Imagen actual" class="mt-2 w-32 h-32 object-cover rounded">
                    <p class="text-sm text-gray-500 mt-1">Sube una nueva imagen solo si deseas cambiarla.</p>
                <?php endif; ?>
                <input type="file" id="foto" name="foto" accept="image/*" class="mt-1 block w-full text-gray-700 dark:text-gray-300">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="juego_detalle.php?id=<?php echo $id; ?>" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Guardar cambios</button>
            </div>
        </form>
    </div>
</body>

</html>