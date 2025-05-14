<?php
session_name("Tienda");
session_start();
include '../includes/connect.php';
include '../includes/idioma.php'; // Incluir el archivo de idioma

// Inicializar variables para evitar errores
$nombre = $descripcion = $precio = '';
$errores = [];
$generos = $plataformas = [];

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $generos = $_POST['generos'] ?? [];
    $plataformas = $_POST['plataformas'] ?? [];
    $foto = $_FILES['foto'] ?? null;

    // Validar campos
    if (empty($nombre)) {
        $errores[] = "El nombre del producto es obligatorio.";
    }
    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    }
    if (empty($precio) || $precio <= 0) {
        $errores[] = "El precio debe ser mayor a 0.";
    }
    if (empty($generos)) {
        $errores[] = "Debes seleccionar al menos un género.";
    }
    if (empty($plataformas)) {
        $errores[] = "Debes seleccionar al menos una plataforma.";
    }
    if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Debes subir una foto del producto.";
    }

    // Si no hay errores, procesar el formulario
    if (empty($errores)) {
        // Subir la foto
        $fotoNombre = uniqid() . '-' . $foto['name'];
        $fotoRuta = '../assets/img/' . $fotoNombre;
        move_uploaded_file($foto['tmp_name'], $fotoRuta);

        // Insertar el producto en la base de datos
        $statement = $pdo->prepare('INSERT INTO producto (nombre, descripcion, precio, foto) VALUES (:nombre, :descripcion, :precio, :foto)');
        $statement->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'foto' => $fotoRuta,
        ]);

        // Obtener el ID del producto insertado
        $productoId = $pdo->lastInsertId();

        // Insertar géneros
        foreach ($generos as $generoId) {
            $statement = $pdo->prepare('INSERT INTO genero_juegos (producto_id, genero_id) VALUES (:producto_id, :genero_id)');
            $statement->execute([
                'producto_id' => $productoId,
                'genero_id' => $generoId,
            ]);
        }

        // Insertar plataformas
        foreach ($plataformas as $plataformaId) {
            $statement = $pdo->prepare('INSERT INTO plataforma_juegos (producto_id, plataforma_id) VALUES (:producto_id, :plataforma_id)');
            $statement->execute([
                'producto_id' => $productoId,
                'plataforma_id' => $plataformaId,
            ]);
        }

        // Redirigir con mensaje de éxito
        header('Location: index.php?mensaje=Producto insertado correctamente');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-md w-full max-w-2xl">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Insertar Producto</h1>

        <!-- Mostrar errores si existen -->
        <?php if (!empty($errores)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php foreach ($errores as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="" method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del producto</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200" >
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200" ><?php echo htmlspecialchars($descripcion); ?></textarea>
            </div>

            <div>
                <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio</label>
                <input type="number" name="precio" id="precio" step=".01" value="<?php echo htmlspecialchars($precio); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200" >
            </div>

            <div>
                <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto del producto</label>
                <input type="file" name="foto" id="foto" class="mt-1 block w-full text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" >
            </div>

            <div>
                <label for="generos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Géneros</label>
                <select name="generos[]" id="generos" multiple class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                    <?php
                    $statement = $pdo->prepare('SELECT * FROM genero');
                    $statement->execute();
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $genero) {
                        $selected = in_array($genero['genero_id'], $generos) ? 'selected' : '';
                        echo '<option value="' . $genero['genero_id'] . '" ' . $selected . '>' . htmlspecialchars($genero['nombre']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="plataformas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plataformas</label>
                <select name="plataformas[]" id="plataformas" multiple class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                    <?php
                    $statement = $pdo->prepare('SELECT * FROM plataforma');
                    $statement->execute();
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $plataforma) {
                        $selected = in_array($plataforma['plataforma_id'], $plataformas) ? 'selected' : '';
                        echo '<option value="' . $plataforma['plataforma_id'] . '" ' . $selected . '>' . htmlspecialchars($plataforma['nombre']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="flex justify-between">
                <a href="index.php" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    Volver
                </a>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Insertar Producto
                </button>
            </div>
        </form>
    </div>
</body>

</html>
