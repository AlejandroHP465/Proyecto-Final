<!-- filepath: c:\xampp\htdocs\Proyecto-Final\views\favoritos.php -->
<?php
session_name("Tienda");
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['cliente_id'])) {
    header('Location: iniciar_sesion.php');
    exit;
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $clienteId = $_SESSION['cliente_id'];
    $productoId = filter_var($_POST['producto_id'], FILTER_VALIDATE_INT);

    if (!$productoId) {
        $_SESSION['mensaje'] = 'ID de producto inválido.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }

    try {
        // Verificar si el producto ya está en favoritos
        $statement = $pdo->prepare('SELECT * FROM favoritos WHERE cliente_id = :cliente_id AND producto_id = :producto_id');
        $statement->execute(['cliente_id' => $clienteId, 'producto_id' => $productoId]);
        $favorito = $statement->fetch(PDO::FETCH_ASSOC);

        if ($favorito) {
            // Si ya está en favoritos, eliminarlo
            $statement = $pdo->prepare('DELETE FROM favoritos WHERE cliente_id = :cliente_id AND producto_id = :producto_id');
            $statement->execute(['cliente_id' => $clienteId, 'producto_id' => $productoId]);
            $_SESSION['mensaje'] = 'Producto eliminado de favoritos.';
        } else {
            // Si no está en favoritos, añadirlo
            $statement = $pdo->prepare('INSERT INTO favoritos (cliente_id, producto_id) VALUES (:cliente_id, :producto_id)');
            $statement->execute(['cliente_id' => $clienteId, 'producto_id' => $productoId]);
            $_SESSION['mensaje'] = 'Producto añadido a favoritos.';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al procesar la solicitud: ' . htmlspecialchars($e->getMessage());
    }

    // Redirigir a la página anterior o al índice
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

// Si se accede directamente al archivo, redirigir al índice
header('Location: index.php');
exit;