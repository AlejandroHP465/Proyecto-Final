<?php
session_name("Tienda");
session_start();
include '../includes/connect.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header('Location: iniciar_sesion.php');
    exit;
}

// Función para registrar errores
function logError($mensaje, $error) {
    error_log("Error en eliminar_usuario.php: " . $mensaje . " - " . $error->getMessage());
    return "Error: " . $mensaje;
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cliente_id'])) {
    $clienteId = intval($_POST['cliente_id']);
    
    // Verificar que el ID del cliente coincida con el de la sesión
    try {
        $stmt = $pdo->prepare('SELECT cliente_id FROM clientes WHERE email = :email');
        $stmt->execute(['email' => $_SESSION['email']]);
        $usuarioActual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuarioActual || $usuarioActual['cliente_id'] !== $clienteId) {
            $_SESSION['error'] = 'No tienes permiso para realizar esta acción.';
            header('Location: perfil_usuario.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = logError("Error al verificar el usuario", $e);
        header('Location: perfil_usuario.php');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Primero eliminar los registros de las tablas dependientes
        try {
            // Eliminar detalles de pedidos primero
            $stmt = $pdo->prepare('DELETE FROM detalles_pedido WHERE pedido_id IN (SELECT pedido_id FROM pedido WHERE cliente_id = :cliente_id)');
            $stmt->execute(['cliente_id' => $clienteId]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar detalles de pedidos: " . $e->getMessage());
        }

        try {
            // Luego eliminar los pedidos
            $stmt = $pdo->prepare('DELETE FROM pedido WHERE cliente_id = :cliente_id');
            $stmt->execute(['cliente_id' => $clienteId]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar pedidos: " . $e->getMessage());
        }

        try {
            // Eliminar reseñas
            $stmt = $pdo->prepare('DELETE FROM resena WHERE cliente_id = :cliente_id');
            $stmt->execute(['cliente_id' => $clienteId]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar reseñas: " . $e->getMessage());
        }

        try {
            // Eliminar valoraciones
            $stmt = $pdo->prepare('DELETE FROM valoraciones WHERE cliente_id = :cliente_id');
            $stmt->execute(['cliente_id' => $clienteId]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar valoraciones: " . $e->getMessage());
        }

        try {
            // Eliminar favoritos
            $stmt = $pdo->prepare('DELETE FROM favoritos WHERE cliente_id = :cliente_id');
            $stmt->execute(['cliente_id' => $clienteId]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar favoritos: " . $e->getMessage());
        }

        try {
            // Eliminar carrito
            $stmt = $pdo->prepare('DELETE FROM carrito WHERE cliente_id = :cliente_id');
            $stmt->execute(['cliente_id' => $clienteId]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar carrito: " . $e->getMessage());
        }

        try {
            // Finalmente, eliminar al cliente
            $stmt = $pdo->prepare('DELETE FROM clientes WHERE cliente_id = :cliente_id');
            $stmt->execute(['cliente_id' => $clienteId]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar cliente: " . $e->getMessage());
        }

        $pdo->commit();

        // Destruir la sesión
        session_destroy();
        
        // Redirigir al usuario a la página de inicio
        header('Location: index.php?mensaje=' . urlencode('Cuenta eliminada correctamente'));
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al eliminar usuario: " . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        header('Location: perfil_usuario.php');
        exit;
    }
} else {
    // Si alguien intenta acceder directamente a esta página, redirigir al perfil
    header('Location: perfil_usuario.php');
    exit;
}
?>