<?php
session_name("Tienda");
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['cliente_id'])) {
    header('Location: ../views/login.php');
    exit;
}

include '../includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_STRING);
        
        if (!$accion) {
            throw new Exception('Acción no válida');
        }

        switch ($accion) {
            case 'añadir':
                $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
                
                if (!$productoId) {
                    throw new Exception('ID de producto no válido');
                }

                // Verificar si el producto existe
                $stmt = $pdo->prepare('SELECT id FROM productos WHERE id = ?');
                $stmt->execute([$productoId]);
                if (!$stmt->fetch()) {
                    throw new Exception('Producto no encontrado');
                }

                // Iniciar transacción
                $pdo->beginTransaction();

                try {
                    // Verificar si el carrito está inicializado
                    if (!isset($_SESSION['carrito'])) {
                        $_SESSION['carrito'] = [];
                    }

                    // Agregar o actualizar el producto en el carrito
                    if (isset($_SESSION['carrito'][$productoId])) {
                        $_SESSION['carrito'][$productoId]['cantidad'] += 1;
                    } else {
                        $_SESSION['carrito'][$productoId] = [
                            'cantidad' => 1
                        ];
                    }

                    // Actualizar en la base de datos
                    $statement = $pdo->prepare('INSERT INTO carrito (cliente_id, producto_id, cantidad) 
                                             VALUES (:cliente_id, :producto_id, :cantidad)
                                             ON DUPLICATE KEY UPDATE cantidad = cantidad + 1');
                    $statement->execute([
                        'cliente_id' => $_SESSION['cliente_id'],
                        'producto_id' => $productoId,
                        'cantidad' => 1
                    ]);

                    $pdo->commit();
                    $_SESSION['mensaje'] = 'Producto añadido al carrito correctamente';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            case 'eliminar':
                $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
                
                if (!$productoId) {
                    throw new Exception('ID de producto no válido');
                }

                $pdo->beginTransaction();

                try {
                    // Eliminar el producto del carrito en la sesión
                    unset($_SESSION['carrito'][$productoId]);

                    // Eliminar el producto del carrito en la base de datos
                    $statement = $pdo->prepare('DELETE FROM carrito WHERE cliente_id = :cliente_id AND producto_id = :producto_id');
                    $statement->execute([
                        'cliente_id' => $_SESSION['cliente_id'],
                        'producto_id' => $productoId
                    ]);

                    $pdo->commit();
                    $_SESSION['mensaje'] = 'Producto eliminado del carrito correctamente';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            case 'vaciar':
                $pdo->beginTransaction();

                try {
                    // Vaciar el carrito en la sesión
                    $_SESSION['carrito'] = [];

                    // Vaciar el carrito en la base de datos
                    $statement = $pdo->prepare('DELETE FROM carrito WHERE cliente_id = :cliente_id');
                    $statement->execute(['cliente_id' => $_SESSION['cliente_id']]);

                    $pdo->commit();
                    $_SESSION['mensaje'] = 'Carrito vaciado correctamente';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            default:
                throw new Exception('Acción no reconocida');
        }

        header('Location: ../views/carrito.php');
        exit;

    } catch (Exception $e) {
        error_log("Error en carrito_controller: " . $e->getMessage());
        $_SESSION['error'] = 'Ha ocurrido un error: ' . $e->getMessage();
        header('Location: ../views/carrito.php');
        exit;
    }
}
?>