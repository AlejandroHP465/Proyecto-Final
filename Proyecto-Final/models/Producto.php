<?php

class Producto
{
    private $id;
    private $nombre;
    private $descripcion;
    private $precio;
    private $foto;
    private $generos;
    private $plataformas;

    /**
     * Constructor de la clase Producto.
     */
    public function __construct($id, $nombre, $descripcion, $precio, $foto, $generos = [], $plataformas = [])
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->foto = $foto;
        $this->generos = $generos;
        $this->plataformas = $plataformas;
    }

    /**
     * Buscar productos por nombre.
     */
    public static function buscarProductos($pdo, $searchQuery = '')
    {
        $query = "SELECT * FROM producto WHERE nombre LIKE :search";
        $statement = $pdo->prepare($query);
        $statement->execute(['search' => '%' . $searchQuery . '%']);
        $resultados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $productos = [];
        foreach ($resultados as $item) {
            $productos[] = new Producto(
                $item['producto_id'],
                $item['nombre'],
                $item['descripcion'],
                $item['precio'],
                $item['Foto']
            );
        }

        // Obtener géneros y plataformas para todos los productos
        self::cargarGenerosYPlataformas($pdo, $productos);

        return $productos;
    }

    /**
     * Cargar géneros y plataformas para una lista de productos.
     */
    private static function cargarGenerosYPlataformas($pdo, &$productos)
    {
        $ids = array_map(fn($producto) => $producto->getId(), $productos);

        if (empty($ids)) {
            return;
        }

        // Obtener géneros
        $queryGeneros = "
            SELECT producto_id, genero.nombre AS genero
            FROM genero
            INNER JOIN genero_juegos ON genero.genero_id = genero_juegos.genero_id
            WHERE producto_id IN (" . implode(',', $ids) . ")";
        $statement = $pdo->query($queryGeneros);
        $generos = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Asignar géneros a los productos
        foreach ($generos as $genero) {
            foreach ($productos as $producto) {
                if ($producto->getId() == $genero['producto_id']) {
                    $producto->añadirGenero($genero['genero']);
                }
            }
        }

        // Obtener plataformas
        $queryPlataformas = "
            SELECT producto_id, plataforma.nombre AS plataforma
            FROM plataforma
            INNER JOIN plataforma_juegos ON plataforma.plataforma_id = plataforma_juegos.plataforma_id
            WHERE producto_id IN (" . implode(',', $ids) . ")";
        $statement = $pdo->query($queryPlataformas);
        $plataformas = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Asignar plataformas a los productos
        foreach ($plataformas as $plataforma) {
            foreach ($productos as $producto) {
                if ($producto->getId() == $plataforma['producto_id']) {
                    $producto->añadirPlataforma($plataforma['plataforma']);
                }
            }
        }
    }

    /**
     * Añadir un género al producto.
     */
    public function añadirGenero($genero)
    {
        if (!in_array($genero, $this->generos)) {
            $this->generos[] = $genero;
        }
    }

    /**
     * Añadir una plataforma al producto.
     */
    public function añadirPlataforma($plataforma)
    {
        if (!in_array($plataforma, $this->plataformas)) {
            $this->plataformas[] = $plataforma;
        }
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function getGeneros()
    {
        return $this->generos;
    }

    public function getPlataformas()
    {
        return $this->plataformas;
    }

    // Setters
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function setGeneros($generos)
    {
        $this->generos = $generos;
    }

    public function setPlataformas($plataformas)
    {
        $this->plataformas = $plataformas;
    }
}
?>