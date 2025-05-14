<footer class="bg-gray-800 text-white py-6">
    <div class="container mx-auto text-center space-y-4">
        <!-- Información básica -->
        <p class="text-sm">
            © <?php echo date('Y'); ?> Ale Games. <?php echo htmlspecialchars($textos[$idioma]['todos_los_derechos_reservados'] ?? 'Todos los derechos reservados.'); ?>
        </p>

        <!-- Enlaces útiles -->
        <div class="flex justify-center space-x-4">
            <a href="politica_privacidad.php" class="text-gray-400 hover:text-white text-sm">
                <?php echo htmlspecialchars($textos[$idioma]['politica_privacidad'] ?? 'Política de privacidad'); ?>
            </a>
            <a href="terminos_condiciones.php" class="text-gray-400 hover:text-white text-sm">
                <?php echo htmlspecialchars($textos[$idioma]['terminos_condiciones'] ?? 'Términos y condiciones'); ?>
            </a>
            <a href="?lang=es" class="text-gray-400 hover:text-white text-sm">
                Español
            </a>
            <a href="?lang=en" class="text-gray-400 hover:text-white text-sm">
                English
            </a>
        </div>

        <!-- Redes sociales -->
        <div class="flex justify-center space-x-4">
            <a href="https://facebook.com" target="_blank" aria-label="Facebook" class="text-gray-400 hover:text-white">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://twitter.com" target="_blank" aria-label="Twitter" class="text-gray-400 hover:text-white">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://instagram.com" target="_blank" aria-label="Instagram" class="text-gray-400 hover:text-white">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>
</footer>

<!-- Agregar FontAwesome para los iconos de redes sociales -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>