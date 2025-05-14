<?php
session_name("Tienda");
session_start();

// Constantes
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('UPLOAD_DIR', '../assets/img/');

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] !== 1) {
    header('Location: ../views/index.php');
    exit;
}

include '../includes/connect.php';

/**
 * Función para validar y subir una imagen
 * @param array $foto Array con la información del archivo
 * @return string|false Ruta de la imagen o false si hay error
 */
function subirImagen($foto) {
    // Validar tamaño
    if ($foto['size'] > MAX_FILE_SIZE) {
        throw new Exception('La imagen excede el tamaño máximo permitido (5MB)');
    }

    // Validar tipo de archivo
    $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        throw new Exception('Solo se permiten archivos JPG, JPEG, PNG y WEBP');
    }

    // Validar que sea una imagen válida
    if (!getimagesize($foto['tmp_name'])) {
        throw new Exception('El archivo no es una imagen válida');
    }

    // Crear directorio si no existe
    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true)) {
        throw new Exception('Error al crear el directorio de imágenes');
    }

    // Generar nombre único y seguro
    $nombreArchivo = uniqid('img_', true) . '.' . $extension;
    $rutaCompleta = UPLOAD_DIR . $nombreArchivo;

    // Intentar mover el archivo
    if (!move_uploaded_file($foto['tmp_name'], $rutaCompleta)) {
        throw new Exception('Error al guardar la imagen');
    }

    return $rutaCompleta;
}

/**
 * Función para validar los datos del juego
 * @param array $datos Array con los datos del juego
 * @return array Array con los errores encontrados
 */
function validarDatosJuego($datos) {
    $errores = [];

    if (empty($datos['nombre']) || strlen($datos['nombre']) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 caracteres';
    }

    if (empty($datos['descripcion']) || strlen($datos['descripcion']) < 10) {
        $errores[] = 'La descripción debe tener al menos 10 caracteres';
    }

    if (!is_numeric($datos['precio']) || $datos['precio'] <= 0) {
        $errores[] = 'El precio debe ser un número positivo';
    }

    return $errores;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_STRING);
        
        if (!$accion) {
            throw new Exception('Acción no válida');
        }

        switch ($accion) {
            case 'insertar':
                $datos = [
                    'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                    'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                    'precio' => filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT),
                    'foto' => $_FILES['foto'] ?? null
                ];

                $errores = validarDatosJuego($datos);
                
                if (empty($errores) && $datos['foto']) {
                    $pdo->beginTransaction();
                    try {
                        $fotoPath = subirImagen($datos['foto']);
                        
                        $statement = $pdo->prepare('
                            INSERT INTO producto (nombre, descripcion, precio, Foto) 
                            VALUES (:nombre, :descripcion, :precio, :foto)
                        ');
                        
                        $statement->execute([
                            'nombre' => $datos['nombre'],
                            'descripcion' => $datos['descripcion'],
                            'precio' => $datos['precio'],
                            'foto' => $fotoPath
                        ]);

                        $pdo->commit();
                        $_SESSION['mensaje'] = '¡Juego insertado con éxito!';
                        header('Location: ../views/insertar_juego.php');
                        exit;
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        if (isset($fotoPath) && file_exists($fotoPath)) {
                            unlink($fotoPath);
                        }
                        throw $e;
                    }
                } else {
                    if (!$datos['foto']) {
                        $errores[] = 'La imagen es obligatoria';
                    }
                    $_SESSION['errores'] = $errores;
                    header('Location: ../views/insertar_juego.php');
                    exit;
                }
                break;

            case 'actualizar':
                $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
                $datos = [
                    'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                    'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                    'precio' => filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT),
                    'foto' => $_FILES['foto'] ?? null
                ];

                $errores = validarDatosJuego($datos);
                
                if (empty($errores) && $productoId) {
                    $pdo->beginTransaction();
                    try {
                        // Obtener la foto actual
                        $stmt = $pdo->prepare('SELECT Foto FROM producto WHERE producto_id = ?');
                        $stmt->execute([$productoId]);
                        $fotoActual = $stmt->fetchColumn();

                        $fotoPath = $fotoActual;
                        if ($datos['foto'] && $datos['foto']['tmp_name']) {
                            $fotoPath = subirImagen($datos['foto']);
                        }

                        $query = 'UPDATE producto SET nombre = :nombre, descripcion = :descripcion, precio = :precio';
                        $params = [
                            'nombre' => $datos['nombre'],
                            'descripcion' => $datos['descripcion'],
                            'precio' => $datos['precio'],
                            'producto_id' => $productoId
                        ];

                        if ($fotoPath !== $fotoActual) {
                            $query .= ', Foto = :foto';
                            $params['foto'] = $fotoPath;
                        }

                        $query .= ' WHERE producto_id = :producto_id';
                        $statement = $pdo->prepare($query);
                        $statement->execute($params);

                        // Eliminar la foto anterior si se subió una nueva
                        if ($fotoPath !== $fotoActual && $fotoActual && file_exists($fotoActual)) {
                            unlink($fotoActual);
                        }

                        $pdo->commit();
                        $_SESSION['mensaje'] = '¡Juego actualizado con éxito!';
                        header('Location: ../views/index.php');
                        exit;
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        if (isset($fotoPath) && $fotoPath !== $fotoActual && file_exists($fotoPath)) {
                            unlink($fotoPath);
                        }
                        throw $e;
                    }
                } else {
                    $_SESSION['errores'] = $errores;
                    header('Location: ../views/actualizar_juegos.php');
                    exit;
                }
                break;

            case 'eliminar':
                $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);

                if ($productoId) {
                    $pdo->beginTransaction();
                    try {
                        // Obtener la foto antes de eliminar
                        $stmt = $pdo->prepare('SELECT Foto FROM producto WHERE producto_id = ?');
                        $stmt->execute([$productoId]);
                        $foto = $stmt->fetchColumn();

                        // Eliminar el producto
                        $statement = $pdo->prepare('DELETE FROM producto WHERE producto_id = :producto_id');
                        $statement->execute(['producto_id' => $productoId]);

                        // Eliminar la foto si existe
                        if ($foto && file_exists($foto)) {
                            unlink($foto);
                        }

                        $pdo->commit();
                        $_SESSION['mensaje'] = '¡Juego eliminado con éxito!';
                        header('Location: ../views/index.php');
                        exit;
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                } else {
                    $_SESSION['errores'] = ['No se pudo identificar el juego a eliminar.'];
                    header('Location: ../views/index.php');
                    exit;
                }
                break;

            default:
                throw new Exception('Acción no reconocida');
        }
    }
} catch (Exception $e) {
    error_log("Error en juegos_controller: " . $e->getMessage());
    $_SESSION['errores'] = ['Error: ' . $e->getMessage()];
    header('Location: ../views/insertar_juego.php');
    exit;
}
?>