<?php
session_name("Tienda");
session_start();
include '../includes/idioma.php'; // Incluir el archivo de idioma
include '../includes/connect.php';
include '../models/Pedido.php'; // Incluir la clase Pedido

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$carrito = &$_SESSION['carrito']; // Usar referencia para mantener sincronizado

// Manejar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminar un producto específico del carrito
    if (isset($_POST['eliminar_producto']) && isset($_POST['producto_id'])) {
        $productoId = $_POST['producto_id'];

        // Verificar si el producto existe en el carrito
        if (isset($carrito[$productoId])) {
            unset($carrito[$productoId]); // Eliminar el producto del carrito
            $_SESSION['carrito'] = $carrito; // Actualizar la sesión
        }

        // Redirigir al carrito después de eliminar el producto
        header('Location: carrito.php');
        exit;
    }

    // Añadir producto al carrito
    if (isset($_POST['producto_id']) && !isset($_POST['eliminar_producto'])) {
        $productoId = $_POST['producto_id'];

        // Verificar si el ID del producto es válido
        if (!$productoId) {
            echo json_encode(['success' => false, 'message' => 'ID de producto no válido.']);
            exit;
        }

        // Añadir el producto al carrito en la sesión
        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad'] += 1;
        } else {
            // Obtener información del producto desde la base de datos
            $statement = $pdo->prepare('SELECT nombre, precio, Foto FROM producto WHERE producto_id = :producto_id');
            $statement->execute(['producto_id' => $productoId]);
            $producto = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
                exit;
            }

            $carrito[$productoId] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'Foto' => $producto['Foto'],
                'cantidad' => 1,
            ];
        }

        $_SESSION['carrito'] = $carrito;

        echo json_encode(['success' => true, 'message' => 'Producto añadido al carrito.']);
        exit;
    }

    // Vaciar el carrito
    if (isset($_POST['vaciar_carrito'])) {
        $_SESSION['carrito'] = [];
        header('Location: carrito.php');
        exit;
    }

    // Procesar el pedido
    if (isset($_POST['comprar'])) {
        try {
            if (empty($carrito)) {
                header('Location: carrito.php');
                exit;
            }

            // Adaptar el array de productos al formato esperado por Pedido::crearPedido
            $productosPedido = [];
            foreach ($carrito as $productoId => $item) {
                $productosPedido[$productoId] = [
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio']
                ];
            }

            // Crear el pedido usando la clase Pedido
            $pedidoId = Pedido::crearPedido($pdo, $_SESSION['cliente_id'], $productosPedido);

            // Vaciar el carrito
            $_SESSION['carrito'] = [];
            $carrito = [];

            echo '<h2 class="text-green-600 text-center">Pedido realizado correctamente</h2>';
        } catch (Exception $e) {
            echo '<div class="text-red-500 text-center">Error al procesar el pedido: ' . $e->getMessage() . '</div>';
        }
    }

    // Confirmar pago
    if (isset($_POST['confirmar_pago'])) {
        $nombreTarjeta = $_POST['nombre_tarjeta'];
        $numeroTarjeta = $_POST['numero_tarjeta'];
        $fechaExpiracion = $_POST['fecha_expiracion'];
        $cvv = $_POST['cvv'];

        // Validar los datos de la tarjeta 
        if (empty($nombreTarjeta) || empty($numeroTarjeta) || empty($fechaExpiracion) || empty($cvv)) {
            echo '<div class="text-red-500 text-center">Por favor, completa todos los campos.</div>';
        } else {
            try {
                if (empty($carrito)) {
                    header('Location: carrito.php');
                    exit;
                }

                // Adaptar el array de productos al formato esperado por Pedido::crearPedido
                $productosPedido = [];
                foreach ($carrito as $productoId => $item) {
                    $productosPedido[$productoId] = [
                        'cantidad' => $item['cantidad'],
                        'precio' => $item['precio']
                    ];
                }

                // Crear el pedido usando la clase Pedido
                $pedidoId = Pedido::crearPedido($pdo, $_SESSION['cliente_id'], $productosPedido);

                // Vaciar el carrito
                $_SESSION['carrito'] = [];
                $carrito = [];

                echo '<h2 class="text-green-600 text-center">Pago realizado correctamente. ¡Gracias por tu compra!</h2>';
            } catch (Exception $e) {
                echo '<div class="text-red-500 text-center">Error al procesar el pago: ' . $e->getMessage() . '</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function abrirModal() {
            document.getElementById('modal-pago').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('modal-pago').classList.add('hidden');
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-blue-600 text-white py-4">
        <div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-2xl font-bold"><?php echo $textos[$idioma]['realizar_pedidos']; ?></h1>
            <a href="index.php" class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">Volver</a>
        </div>
    </header>

    <main class="container mx-auto py-8 px-4">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Carrito de Compras</h2>

        <?php if (!empty($carrito)): ?>
            <form method="POST" class="mb-4">
                <button type="submit" name="vaciar_carrito" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded">
                    🗑️ Vaciar Carrito
                </button>
            </form>

            <table class="table-auto w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2">Foto</th>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Precio</th>
                        <th class="px-4 py-2">Cantidad</th>
                        <th class="px-4 py-2">Total</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalCarrito = 0;
                    foreach ($carrito as $productoId => $item):
                        $totalProducto = $item['precio'] * $item['cantidad'];
                        $totalCarrito += $totalProducto;
                    ?>
                        <tr class="border-b">
                            <td class="px-4 py-2">
                                <img src="<?php echo htmlspecialchars($item['Foto']); ?>" alt="Foto del producto" class="w-16 h-16 object-cover rounded">
                            </td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td class="px-4 py-2"><?php echo number_format($item['precio'], 2, ',', '.'); ?>€</td>
                            <td class="px-4 py-2"><?php echo $item['cantidad']; ?></td>
                            <td class="px-4 py-2"><?php echo number_format($totalProducto, 2, ',', '.'); ?>€</td>
                            <td class="px-4 py-2">
                                <form method="POST">
                                    <input type="hidden" name="producto_id" value="<?php echo $productoId; ?>">
                                    <button type="submit" name="eliminar_producto" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100">
                        <td colspan="4" class="px-4 py-2 font-bold">Total</td>
                        <td class="px-4 py-2 font-bold"><?php echo number_format($totalCarrito, 2, ',', '.'); ?>€</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-6 flex justify-end">
                <button type="button" onclick="abrirModal()" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md">
                    Pagar
                </button>
            </div>

            <!-- Modal -->
            <div id="modal-pago" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                <div class="bg-white rounded-lg shadow-lg w-96 p-6">
                    <h2 class="text-xl font-bold mb-4">Datos de Pago</h2>
                    <form method="POST">
                        <div class="mb-4">
                            <label for="nombre-tarjeta" class="block text-sm font-medium text-gray-700">Nombre en la tarjeta</label>
                            <input type="text" id="nombre-tarjeta" name="nombre_tarjeta" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="numero-tarjeta" class="block text-sm font-medium text-gray-700">Número de tarjeta</label>
                            <input type="text" id="numero-tarjeta" name="numero_tarjeta" maxlength="16" pattern="\d{16}" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16);" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" >
                        </div>
                        <div class="mb-4 flex space-x-4">
                            <div>
                                <label for="fecha-expiracion" class="block text-sm font-medium text-gray-700">Fecha de expiración</label>
                                <input type="text" id="fecha-expiracion" name="fecha_expiracion" maxlength="5" pattern="^(0[1-9]|1[0-2])\/\d{2}$"
                                    oninput="this.value = this.value.replace(/[^0-9\/]/g, '').slice(0,5);" 
                                    placeholder="MM/AA"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" >
                            </div>
                            <div>
                                <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                                <input type="text" id="cvv" name="cvv" maxlength="3" pattern="\d{3}" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,3);" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" >
                            </div>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="cerrarModal()" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md">
                                Cancelar
                            </button>
                            <button type="submit" name="confirmar_pago" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md">
                                Confirmar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <h2 class="text-center text-gray-600">No tienes elementos en el carrito</h2>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-800 text-white py-4 text-center">
        <p>© Ale Games 2024</p>
    </footer>
</body>
</html>
