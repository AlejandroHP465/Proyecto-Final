// Menú de idioma (ajustes)
document.addEventListener('DOMContentLoaded', function() {
    const settingsButton = document.getElementById('settingsButton');
    const settingsMenu = document.getElementById('settingsMenu');
    if (settingsButton && settingsMenu) {
        settingsButton.addEventListener('click', function(e) {
            e.stopPropagation();
            settingsMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', function(e) {
            if (!settingsMenu.contains(e.target) && !settingsButton.contains(e.target)) {
                settingsMenu.classList.add('hidden');
            }
        });
    }

    // Loader al enviar formularios
    const forms = document.querySelectorAll('form');
    forms.forEach((form) => {
        form.addEventListener('submit', () => {
            const loader = document.getElementById('loader');
            if (loader) loader.classList.remove('hidden');
        });
    });

    // Ocultar loader después de un tiempo (opcional)
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 2000);
    }
});

// Mostrar loader antes de recargar o navegar
window.addEventListener('beforeunload', () => {
    const loader = document.getElementById('loader');
    if (loader) loader.classList.remove('hidden');
});

// Notificaciones
function mostrarNotificacion(tipo, mensaje) {
    const notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) return;
    const notification = document.createElement('div');
    notification.className = tipo === 'success' ? 'bg-green-500' : 'bg-red-500';
    notification.className += ' text-white px-4 py-2 rounded shadow-md mb-2';
    notification.textContent = mensaje;
    notificationContainer.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Añadir al carrito (AJAX)
function handleAddToCart(event, productName, productId) {
    event.preventDefault();
    fetch('../views/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `producto_id=${productId}`,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                mostrarNotificacion('success', `${productName} se añadió al carrito.`);
            } else {
                mostrarNotificacion('error', data.message || 'Error al añadir el producto al carrito.');
            }
        })
        .catch(() => {
            mostrarNotificacion('error', 'Hubo un problema al añadir el producto al carrito.');
        });
}

// Estrellas de valoración
function rellenarEstrellas(valor) {
    const estrellas = document.querySelectorAll('.estrella');
    estrellas.forEach((estrella, index) => {
        if (index < valor) {
            estrella.classList.add('text-yellow-500');
        } else {
            estrella.classList.remove('text-yellow-500');
        }
    });
}

// Modal de pago
function abrirModal() {
    const modal = document.getElementById('modal-pago');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
    }
}
function cerrarModal() {
    const modal = document.getElementById('modal-pago');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }
}

// Paginación (solo si usas .product y #paginacion)
document.addEventListener('DOMContentLoaded', function() {
    const productos = Array.from(document.querySelectorAll('.product'));
    let productosFiltrados = [...productos];
    const productosPorPagina = 9;
    let paginaActual = 1;

    function mostrarProductos(pagina) {
        const inicio = (pagina - 1) * productosPorPagina;
        const fin = inicio + productosPorPagina;
        const productosPagina = productosFiltrados.slice(inicio, fin);
        productos.forEach(producto => { producto.style.display = 'none'; });
        productosPagina.forEach(producto => { producto.style.display = ''; });
    }

    function crearPaginacion() {
        const totalPaginas = Math.ceil(productosFiltrados.length / productosPorPagina);
        const paginacion = document.getElementById('paginacion');
        if (!paginacion) return;
        paginacion.innerHTML = '';

        if (paginaActual > 1) {
            const botonAnterior = document.createElement('button');
            botonAnterior.innerHTML = '&laquo;';
            botonAnterior.className = 'px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600';
            botonAnterior.addEventListener('click', () => {
                paginaActual--;
                mostrarProductos(paginaActual);
                crearPaginacion();
            });
            paginacion.appendChild(botonAnterior);
        }

        for (let i = 1; i <= totalPaginas; i++) {
            const boton = document.createElement('button');
            boton.innerText = i;
            boton.className = `px-4 py-2 rounded ${paginaActual === i ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}`;
            boton.addEventListener('click', () => {
                paginaActual = i;
                mostrarProductos(paginaActual);
                crearPaginacion();
            });
            paginacion.appendChild(boton);
        }

        if (paginaActual < totalPaginas) {
            const botonSiguiente = document.createElement('button');
            botonSiguiente.innerHTML = '&raquo;';
            botonSiguiente.className = 'px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600';
            botonSiguiente.addEventListener('click', () => {
                paginaActual++;
                mostrarProductos(paginaActual);
                crearPaginacion();
            });
            paginacion.appendChild(botonSiguiente);
        }
    }

    if (productos.length > 0) {
        mostrarProductos(paginaActual);
        crearPaginacion();
    }
});