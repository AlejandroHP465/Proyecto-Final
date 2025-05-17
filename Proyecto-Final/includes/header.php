<header class="bg-blue-600 text-white py-4">
    <div class="container mx-auto flex justify-between items-center px-4">
        <!-- Sección izquierda: Saludo y perfil -->
        <div class="flex items-center space-x-4">
            <?php if (isset($_SESSION['usuario'])): ?>
                <p class="text-lg font-semibold">
                    <?php echo htmlspecialchars($textos[$idioma]['saludo']); ?>, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                </p>
                <a href="../views/perfil_usuario.php" aria-label="Perfil de Usuario">
                    <img src="https://cdn-icons-png.flaticon.com/512/74/74472.png" class="w-10 h-10 rounded-full" alt="Perfil de Usuario">
                </a>
            <?php endif; ?>
        </div>

        <!-- Sección derecha: Ajustes y botones -->
        <div class="flex space-x-4 items-center">
            <!-- Icono de ajustes -->
            <div class="relative">
                <button id="settingsButton" class="p-2 rounded-full hover:bg-gray-700" aria-label="Cambiar idioma">
                    <?php echo $idioma === 'en' ? '🇪🇸' : '🇬🇧'; ?>
                </button>
                <div id="settingsMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <?php
                    // Obtener la URL actual
                    $currentUrl = $_SERVER['REQUEST_URI'];
                    // Eliminar cualquier parámetro de idioma existente
                    $baseUrl = preg_replace('/([?&])lang=[^&]+(&|$)/', '$1', $currentUrl);
                    // Añadir el signo de interrogación si no existe
                    $baseUrl = strpos($baseUrl, '?') === false ? $baseUrl . '?' : $baseUrl;
                    // Eliminar & extra al final si existe
                    $baseUrl = rtrim($baseUrl, '&?');
                    // Añadir el separador correcto
                    $separator = strpos($baseUrl, '?') === false ? '?' : '&';
                    // Construir la URL final
                    $newLang = $idioma === 'en' ? 'es' : 'en';
                    $finalUrl = $baseUrl . $separator . 'lang=' . $newLang;
                    ?>
                    <a href="<?php echo htmlspecialchars($finalUrl); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                        <?php echo $idioma === 'en' ? 'Español 🇪🇸' : 'English 🇬🇧'; ?>
                    </a>
                </div>
            </div>

            <!-- Botones de sesión -->
            <?php if (isset($_SESSION['email'])): ?>
                <form method="POST" action="../views/index.php" style="display:inline;">
                    <button type="submit" name="cerrar" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded">
                        <?php echo htmlspecialchars($textos[$idioma]['cerrar_sesion']); ?>
                    </button>
                </form>
                <a href="actualizar_datos.php" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded">
                    <?php echo htmlspecialchars($textos[$idioma]['actualizar_datos']); ?>
                </a>
                <?php if ($_SESSION['email'] === 'root@email.com'): ?>
                    <a href="insertar_juego.php" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                        <?php echo htmlspecialchars($textos[$idioma]['insertar_juego']); ?>
                    </a>
                <?php else: ?>
                    <a href="carrito.php" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                        <?php echo htmlspecialchars($textos[$idioma]['realizar_pedidos']); ?>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="iniciar_sesion.php" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    <?php echo htmlspecialchars($textos[$idioma]['iniciar_sesion']); ?>
                </a>
                <a href="registrar.php" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    <?php echo htmlspecialchars($textos[$idioma]['registrar']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
