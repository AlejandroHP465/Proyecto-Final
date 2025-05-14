<?php
session_name("Tienda");
session_start();

include '../includes/connect.php';
include '../models/Usuario.php';  // Asegúrate de que la ruta a Usuario.php es correcta

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogemos y saneamos los datos
    $nombre               = trim($_POST["nombre"] ?? '');
    $email                = trim($_POST["email"] ?? '');
    $telefono             = trim($_POST["telefono"] ?? '');
    $contrasena           = $_POST["contraseña"] ?? '';
    $confirmarContrasena  = $_POST["confirmar_contraseña"] ?? '';

    $errores = [];

    // Validación del nombre
    if (empty($nombre)) {
        $errores[] = 'El campo nombre es obligatorio.';
    } elseif (strlen($nombre) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 caracteres.';
    }

    // Validación del email
    if (empty($email)) {
        $errores[] = 'El campo email es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El formato del email no es válido.';
    } else {
        // Verificar si el email ya está registrado
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $errores[] = 'Este email ya está registrado en el sistema.';
        }
    }

    // Validación del teléfono
    if (empty($telefono)) {
        $errores[] = 'El campo teléfono es obligatorio.';
    } elseif (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores[] = 'El teléfono debe contener exactamente 9 números.';
    }

    // Validación de las contraseñas
    if (empty($contrasena)) {
        $errores[] = 'El campo contraseña es obligatorio.';
    } elseif (strlen($contrasena) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if (empty($confirmarContrasena)) {
        $errores[] = 'Debe confirmar la contraseña.';
    } elseif ($contrasena !== $confirmarContrasena) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    // Si hay errores, los guardamos y redirigimos
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['old_input'] = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono
        ];
        header('Location: ../views/registrar.php');
        exit;
    }

    // Si no hay errores, intentamos el registro
    try {
        $ok = Usuario::registrar($pdo, $nombre, $email, $telefono, $contrasena);
        if ($ok) {
            $_SESSION['mensaje'] = '¡Registro exitoso! Por favor, inicia sesión para continuar.';
            header('Location: ../views/iniciar_sesion.php');
            exit;
        } else {
            throw new Exception('Error al registrar el usuario.');
        }
    } catch (Exception $e) {
        $_SESSION['errores'] = ['Ha ocurrido un error durante el registro. Por favor, inténtalo de nuevo más tarde.'];
        $_SESSION['old_input'] = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono
        ];
        header('Location: ../views/registrar.php');
        exit;
    }
}
