<?php

class Pedido
{
    private $id;
    private $clienteId;
    private $fecha;
    private $productos;

    /**
     * Constructor de la clase Pedido.
     */
    public function __construct($id, $clienteId, $fecha, $productos = [])
    {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->fecha = $fecha;
        $this->productos = $productos;
    }

    /**
     * Crear un nuevo pedido.
     */
    public static function crearPedido($pdo, $clienteId, $productos)
    {
        try {
            $pdo->beginTransaction();

            // Insertar el pedido
            $query = "INSERT INTO pedido (cliente_id, fecha_pedido) VALUES (:cliente_id, NOW())";
            $statement = $pdo->prepare($query);
            $statement->bindParam(':cliente_id', $clienteId);
            $statement->execute();

            $pedidoId = $pdo->lastInsertId();

            // Insertar los productos del pedido
            $query = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (:pedido_id, :producto_id, :cantidad, :precio)";
            $statement = $pdo->prepare($query);

            foreach ($productos as $productoId => $producto) {
                $statement->execute([
                    'pedido_id' => $pedidoId,
                    'producto_id' => $productoId,
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio']
                ]);
            }

            $pdo->commit();
            return $pedidoId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Error al crear el pedido: " . $e->getMessage());
        }
    }

    /**
     * Obtener pedidos por cliente.
     */
    public static function obtenerPorCliente($pdo, $clienteId)
    {
        $query = "SELECT * FROM pedidos WHERE cliente_id = :cliente_id ORDER BY fecha DESC";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':cliente_id', $clienteId);
        $statement->execute();

        $pedidos = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = new Pedido($row['pedido_id'], $row['cliente_id'], $row['fecha']);
        }

        return $pedidos;
    }

    /**
     * Obtener detalles de un pedido específico.
     */
    public static function obtenerDetalles($pdo, $pedidoId)
    {
        $query = "SELECT p.nombre, pp.cantidad, pp.precio 
                  FROM pedido_productos pp
                  INNER JOIN producto p ON pp.producto_id = p.producto_id
                  WHERE pp.pedido_id = :pedido_id";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':pedido_id', $pedidoId);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todos los pedidos.
     */
    public static function listarTodos($pdo)
    {
        $query = "SELECT * FROM pedidos ORDER BY fecha DESC";
        $statement = $pdo->query($query);

        $pedidos = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = new Pedido($row['pedido_id'], $row['cliente_id'], $row['fecha']);
        }

        return $pedidos;
    }

    /**
     * Calcular el total del pedido.
     */
    public function calcularTotal()
    {
        $total = 0;
        foreach ($this->productos as $producto) {
            $total += $producto['precio'] * $producto['cantidad'];
        }
        return $total;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getClienteId()
    {
        return $this->clienteId;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getProductos()
    {
        return $this->productos;
    }

    // Setters
    public function setProductos($productos)
    {
        $this->productos = $productos;
    }
}
?>