<?php
// Constantes
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('UPLOAD_DIR', './imagen/');

$errores = [];
$nombreFinal = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización y validación de datos
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $generos = filter_input(INPUT_POST, 'generos', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];
    $plataformas = filter_input(INPUT_POST, 'plataformas', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];
    $foto = $_FILES['foto'] ?? null;

    // Validaciones
    if (empty($nombre) || strlen($nombre) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 caracteres';
    }

    if (empty($descripcion) || strlen($descripcion) < 10) {
        $errores[] = 'La descripción debe tener al menos 10 caracteres';
    }

    if ($precio === false || $precio <= 0) {
        $errores[] = 'El precio debe ser un número positivo';
    }

    if (empty($generos)) {
        $errores[] = 'Debe seleccionar al menos un género';
    }

    if (empty($plataformas)) {
        $errores[] = 'Debe seleccionar al menos una plataforma';
    }

    // Validación de la imagen
    if (!$foto) {
        $errores[] = 'La foto es obligatoria';
    } else {
        if ($foto['error'] === 0) {
            // Verificar tamaño
            if ($foto['size'] > MAX_FILE_SIZE) {
                $errores[] = 'La imagen excede el tamaño máximo permitido (5MB)';
            }

            // Verificar tipo de archivo
            $imagenInfo = pathinfo($foto['name']);
            $extension = strtolower($imagenInfo['extension']);
            
            if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                $errores[] = 'Solo se permiten archivos JPG, JPEG, PNG y WEBP';
            }

            // Verificar que sea una imagen válida
            if (!getimagesize($foto['tmp_name'])) {
                $errores[] = 'El archivo no es una imagen válida';
            }

            // Crear directorio si no existe
            if (!is_dir(UPLOAD_DIR)) {
                if (!mkdir(UPLOAD_DIR, 0755, true)) {
                    $errores[] = 'Error al crear el directorio de imágenes';
                }
            }

            // Generar nombre único y seguro
            if (empty($errores)) {
                $nombreFinal = UPLOAD_DIR . uniqid('img_', true) . '.' . $extension;
                
                // Intentar mover el archivo
                if (!move_uploaded_file($foto['tmp_name'], $nombreFinal)) {
                    $errores[] = 'Error al guardar la imagen';
                    $nombreFinal = null;
                }
            }
        } else {
            $errores[] = 'Error al subir la imagen: ' . $foto['error'];
        }
    }

    // Si no hay errores, procesar los datos
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            // Insertar nuevo producto
            $statement = $pdo->prepare('
                INSERT INTO producto (nombre, descripcion, precio, foto) 
                VALUES (:nombre, :descripcion, :precio, :foto)
            ');
            
            $statement->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'foto' => $nombreFinal
            ]);

            $id = $pdo->lastInsertId();

            // Insertar géneros
            $stmtGeneros = $pdo->prepare('
                INSERT INTO genero_juegos (genero_id, producto_id) 
                VALUES (:genero, :producto)
            ');

            foreach ($generos as $genero) {
                $stmtGeneros->execute([
                    'genero' => $genero,
                    'producto' => $id
                ]);
            }

            // Insertar plataformas
            $stmtPlataformas = $pdo->prepare('
                INSERT INTO plataforma_juegos (plataforma_id, producto_id) 
                VALUES (:plataforma, :producto)
            ');

            foreach ($plataformas as $plataforma) {
                $stmtPlataformas->execute([
                    'plataforma' => $plataforma,
                    'producto' => $id
                ]);
            }

            $pdo->commit();
            $_SESSION['mensaje'] = 'Juego insertado correctamente';
            header('Location: index.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            // Si hay error, eliminar la imagen subida
            if ($nombreFinal && file_exists($nombreFinal)) {
                unlink($nombreFinal);
            }
            error_log("Error al insertar juego: " . $e->getMessage());
            $errores[] = 'Error al procesar la solicitud: ' . $e->getMessage();
        }
    }
}
?>
