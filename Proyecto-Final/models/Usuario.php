<?php

class Usuario
{
    private $id;
    private $nombre;
    private $email;
    private $telefono;

    /**
     * Constructor de la clase Usuario.
     */
    public function __construct($id, $nombre, $email, $telefono)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
    }

    /**
     * Registrar un nuevo usuario en la base de datos.
     */
    public static function registrar($pdo, $nombre, $email, $telefono, $contrasena)
    {
        $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

        $query = "INSERT INTO clientes (nombre, email, telefono, contrasena) VALUES (:nombre, :email, :telefono, :contrasena)";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':nombre', $nombre);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':telefono', $telefono);
        $statement->bindParam(':contrasena', $hashedPassword);

        return $statement->execute();
    }

    /**
     * Iniciar sesión con un correo electrónico y contraseña.
     */
    public static function iniciarSesion($pdo, $email, $contrasena)
    {
        $query = "SELECT * FROM clientes WHERE email = :email";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $usuario = $statement->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            return new Usuario($usuario['cliente_id'], $usuario['nombre'], $usuario['email'], $usuario['telefono']);
        }

        return null;
    }

    /**
     * Verificar si un correo electrónico ya está registrado.
     */
    public static function emailExiste($pdo, $email)
    {
        $query = "SELECT COUNT(*) FROM clientes WHERE email = :email";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':email', $email);
        $statement->execute();

        return $statement->fetchColumn() > 0;
    }

    /**
     * Actualizar los datos del usuario.
     */
    public function actualizar($pdo, $nombre, $email, $telefono, $contrasena = null)
    {
        $query = "UPDATE clientes SET nombre = :nombre, email = :email, telefono = :telefono";
        if ($contrasena) {
            $query .= ", contrasena = :contrasena";
        }
        $query .= " WHERE cliente_id = :id";

        $statement = $pdo->prepare($query);
        $statement->bindParam(':nombre', $nombre);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':telefono', $telefono);
        $statement->bindParam(':id', $this->id);

        if ($contrasena) {
            $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);
            $statement->bindParam(':contrasena', $hashedPassword);
        }

        return $statement->execute();
    }

    /**
     * Obtener el ID del usuario.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtener el nombre del usuario.
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Establecer el nombre del usuario.
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Obtener el correo electrónico del usuario.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Establecer el correo electrónico del usuario.
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Obtener el teléfono del usuario.
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Establecer el teléfono del usuario.
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
}
?>