<?php
session_name("Tienda");
session_start();
include '../includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $carrito = $_SESSION['carrito'] ?? [];

    // Validar que el carrito no esté vacío
    if (empty($carrito)) {
        $_SESSION['errores'] = ['El carrito está vacío.'];
        header('Location: ../views/realizar_pedido.php');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Insertar el pedido
        $statement = $pdo->prepare('INSERT INTO pedidos (cliente_id, fecha) VALUES (:cliente_id, NOW())');
        $statement->execute(['cliente_id' => $_SESSION['cliente_id']]);
        $pedidoId = $pdo->lastInsertId();

        // Preparar la consulta para insertar productos
        $productoStmt = $pdo->prepare('SELECT precio FROM producto WHERE producto_id = :producto_id');
        $insertStmt = $pdo->prepare('INSERT INTO pedido_productos (pedido_id, producto_id, cantidad, precio) VALUES (:pedido_id, :producto_id, :cantidad, :precio)');

        foreach ($carrito as $productoId => $producto) {
            // Validar que el producto exista en la base de datos
            $productoStmt->execute(['producto_id' => $productoId]);
            $productoData = $productoStmt->fetch(PDO::FETCH_ASSOC);

            if (!$productoData) {
                throw new Exception('Producto no encontrado: ' . htmlspecialchars($productoId));
            }

            // Insertar el producto en el pedido
            $insertStmt->execute([
                'pedido_id' => $pedidoId,
                'producto_id' => $productoId,
                'cantidad' => $producto['cantidad'],
                'precio' => $productoData['precio']
            ]);
        }

        $pdo->commit();

        // Vaciar el carrito
        $_SESSION['carrito'] = [];

        $_SESSION['mensaje'] = '¡Pedido realizado con éxito!';
        header('Location: ../views/pedido_exitoso.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['errores'] = ['Error al procesar el pedido: ' . htmlspecialchars($e->getMessage())];
        header('Location: ../views/realizar_pedido.php');
        exit;
    }
}
?>