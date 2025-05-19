# Proyecto Final - Tienda de Videojuegos

Este proyecto es una tienda de videojuegos en línea desarrollada como proyecto final. Permite a los usuarios explorar el catálogo, gestionar su cuenta, realizar compras y a los administradores gestionar productos y pedidos.

---

## Características principales

### Para usuarios
- Navegación y búsqueda en el catálogo de videojuegos.
- Visualización de detalles de cada juego (descripción, géneros, plataformas, valoraciones y reseñas).
- Añadir y eliminar productos del carrito.
- Realizar pedidos y consultar historial.
- Gestión de cuenta personal (actualizar datos, eliminar cuenta).
- Lista de favoritos y gestión de reseñas.
- Interfaz multilenguaje (Español/Inglés).

### Para administradores
- Gestión completa de productos: añadir, editar y eliminar videojuegos.
- Visualización y gestión de pedidos realizados por los usuarios.
- Acceso a panel de administración.

---

## Tecnologías utilizadas

- **Frontend:** HTML5, CSS3 (TailwindCSS), JavaScript.
- **Backend:** PHP 8+ (PDO para acceso a base de datos).
- **Base de datos:** MySQL.
- **Servidor local:** XAMPP.
- **Gestión de sesiones y autenticación:** PHP Sessions.

---

## Instalación y configuración

1. **Clona este repositorio en tu servidor local:**
   ```bash
   git clone https://github.com/AlejandroHP465/Proyecto-Final.git
   ```

2. **Configura la base de datos:**
   - Importa el archivo `database.sql` (incluido en el proyecto) en tu servidor MySQL.
   - Ajusta los parámetros de conexión en `includes/connect.php` según tu entorno local.

3. **Configura el entorno:**
   - Asegúrate de que XAMPP (o tu servidor local) esté ejecutando Apache y MySQL.
   - Coloca la carpeta del proyecto en el directorio `htdocs` de XAMPP.

4. **Accede a la aplicación:**

   - **Desde GitHub (entorno local típico):**
     1. Clona el repositorio en tu carpeta `htdocs` de XAMPP:
        ```bash
        git clone https://github.com/AlejandroHP465/Proyecto-Final.git
        ```
     2. Abre tu navegador y entra en:  
        `http://localhost/Proyecto-Final/Proyecto-Final/views/index.php`

   - **Desde la ruta local :**
     1. Copia la carpeta del proyecto a:  
        `C:\xampp\htdocs\PRW_HigueroPazAlejandro\Proyecto-Final\Proyecto-Final`
     2. Abre tu navegador y entra en:  
        `http://localhost/PRW_HigueroPazAlejandro/Proyecto-Final/Proyecto-Final/views/index.php`

---

## Estructura del proyecto

```
Proyecto-Final/
│
├── assets/
│   └── js/           # Scripts JavaScript
├── controllers/      # Lógica de negocio (carrito, pedidos, usuarios, etc.)
├── includes/         # Archivos comunes (header, footer, idioma, conexión)
├── models/           # Clases PHP para entidades (Producto, Usuario, Pedido)
├── views/            # Vistas principales (index, perfil, carrito, etc.)
├── README.md
└── database.sql      # Script para crear e inicializar la base de datos
```

---

## Usuarios predefinidos para pruebas

### Administrador (Root)
- **Email:** `root@email.com`
- **Contraseña:** `root123`
- **Rol:** Administrador (puede gestionar productos y pedidos).

### Usuarios regulares
| Nombre            | Email                     | Contraseña | Teléfono    |
|-------------------|---------------------------|------------|-------------|
| Juan Pérez        | juan.perez@example.com    | pass1      | 600123456   |
| María García      | maria.garcia@example.com  | pass2      | 600234567   |
| Carlos Sánchez    | carlos.sanchez@example.com| pass3      | 600345678   |
| Lucía Martínez    | lucia.martinez@example.com| pass4      | 600456789   |
| David López       | david.lopez@example.com   | pass5      | 600567890   |
| Ana Fernández     | ana.fernandez@example.com | pass6      | 600678901   |
| José Rodríguez    | jose.rodriguez@example.com| pass7      | 600789012   |
| Laura Gómez       | laura.gomez@example.com   | pass8      | 600890123   |
| Pablo Díaz        | pablo.diaz@example.com    | pass9      | 600901234   |
| Elena Ruiz        | elena.ruiz@example.com    | pass10     | 600012345   |

> **Nota:** Las contraseñas están encriptadas en la base de datos, pero las proporcionadas aquí son las originales para pruebas.

---

## Funcionalidades destacadas

- **Soporte multilenguaje:** Cambia entre español e inglés desde cualquier página.
- **Gestión de favoritos y reseñas:** Los usuarios pueden marcar juegos como favoritos y dejar reseñas.
- **Panel de administración:** Acceso exclusivo para el usuario root para gestionar el catálogo y pedidos.
- **Notificaciones y validaciones:** Mensajes claros para acciones exitosas o errores.
- **Diseño responsive:** Adaptado para dispositivos móviles y escritorio.

---
`
