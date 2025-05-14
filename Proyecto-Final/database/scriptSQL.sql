CREATE DATABASE videojuegos;
USE videojuegos;
-- Tabla de Clientes
CREATE TABLE clientes (
    cliente_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100) UNIQUE
);

-- Tabla de Géneros
CREATE TABLE genero (
    genero_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de Plataformas
CREATE TABLE plataforma (
    plataforma_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de Productos
CREATE TABLE producto (
    producto_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
Foto text
);

-- Tabla de Pedidos
CREATE TABLE pedido (
    pedido_id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT,
    fecha_pedido DATE NOT NULL,
    total DECIMAL(10, 2),
    estado VARCHAR(50),
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id)
);

CREATE TABLE carrito (
    cliente_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    PRIMARY KEY (cliente_id, producto_id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES producto(producto_id) ON DELETE CASCADE
);


-- Tabla de Detalles de Pedido
CREATE TABLE detalles_pedido (
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) AS (cantidad * precio_unitario),
    PRIMARY KEY (pedido_id, producto_id),
    FOREIGN KEY (pedido_id) REFERENCES pedido(pedido_id),
    FOREIGN KEY (producto_id) REFERENCES producto(producto_id)
);

CREATE TABLE genero_juegos (
  producto_id   INT NOT NULL,
  genero_id     INT NOT NULL,
  PRIMARY KEY (producto_id, genero_id),
  FOREIGN KEY (producto_id) REFERENCES producto(producto_id) ON DELETE CASCADE,
  FOREIGN KEY (genero_id)   REFERENCES genero(genero_id)   ON DELETE CASCADE
);

CREATE TABLE plataforma_juegos (
  producto_id    INT NOT NULL,
  plataforma_id  INT NOT NULL,
  PRIMARY KEY (producto_id, plataforma_id),
  FOREIGN KEY (producto_id)   REFERENCES producto(producto_id)   ON DELETE CASCADE,
  FOREIGN KEY (plataforma_id) REFERENCES plataforma(plataforma_id) ON DELETE CASCADE
);


-- Tabla de Valoraciones
CREATE TABLE valoraciones (
    valoracion_id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    cliente_id INT NOT NULL,
    valoracion DECIMAL(2, 1) NOT NULL, -- Permite valores como 3.5
    FOREIGN KEY (producto_id) REFERENCES producto(producto_id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id)
);

-- Tabla de Favoritos
CREATE TABLE favoritos (
    favorito_id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    producto_id INT NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
    FOREIGN KEY (producto_id) REFERENCES producto(producto_id)
);



-- Tabla de resena
CREATE TABLE resena (
    resena_id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    cliente_id INT NOT NULL,
    resena TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES producto(producto_id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id)
);

-- Plataforma_juegos (producto_id, plataforma_id) y Genero_juegos (producto_id, genero_id)
-- Para cada uno de los 50 juegos 



INSERT INTO plataforma (nombre) VALUES

('PC'),
('PlayStation 4'),
('PlayStation 5'),
('Nintendo Switch'),
('Xbox One'),
('Xbox Series X/S');


INSERT INTO genero (nombre) VALUES 

('Aventura'), 
('Acción'), 
('RPG'), 
('Deportes'), 
('Simulación'), 
('Terror'), 
('Estrategia'), 
('Lucha'), 
('Multijugador'),
('Rol'),
('Shooter'),
('Horror'),
('Carreras'),
('Indie');

INSERT INTO clientes (nombre, contrasena, telefono, email) VALUES 

('root', '$2y$10$IbXtFZ67Lkw6zJb/7dsTp.w7H.QLWYygI8U56XYrRb6YlQ.gQHzka', '928164348', 'root@email.com'), -- Contraseña 'root'
('Juan Pérez', '$2b$12$mExVU9AP316S1UoDFYPgM.SWvEck7QOAlT3e/ydS4eafK.yJuo2sW', '600123456', 'juan.perez@example.com'), -- Contraseña pass1
('María García', '$2b$12$y9YBEbMPesEAt/YJwL.N0.LMOqyBWQGaGKbVh6tehQFDfGko9Y5/m', '600234567', 'maria.garcia@example.com'),-- Contraseña pass2
('Carlos Sánchez', '$2b$12$qJY4rAQyjUioqFxqy9i8S.V/8SFBaH08xqycP2XHCWbU2ZsXbLjkO', '600345678', 'carlos.sanchez@example.com'), -- Contraseña pass3
('Lucía Martínez', '$2b$12$mvF6acLxdQ6W.TxQbKSG2.mrcGHPj5wu/ql9YUaXbRtgFaHPh714e', '600456789', 'lucia.martinez@example.com'), -- Contraseña pass4
('David López', '$2b$12$gf.NNkH1cNxihbMesPQDyuXG4obeT8PHUTKlWHzVy8.dVQZjaDgvS', '600567890', 'david.lopez@example.com'), -- Contraseña pass5
('Ana Fernández', '$2b$12$l2snDOIvRFOjxyFTXUz5NufoQNQNXdgNW.oYIw.YG7dpkxyVkHMwK', '600678901', 'ana.fernandez@example.com'), -- Contraseña pass6
('José Rodríguez', '$2b$12$/L.4pfaaiSK7iD7jXQ0/p.Dk1j3vocYBa5RbTGqXCTe7psMbhNikK', '600789012', 'jose.rodriguez@example.com'), -- Contraseña pass7
('Laura Gómez', '$2b$12$TWKnsCPu3BjIeHUzUx.NkuUA4QQqcgP22P7KmSADu2VC1DODXb95S', '600890123', 'laura.gomez@example.com'), -- contraseña pass8
('Pablo Díaz', '$2b$12$X2.fyGcNW6kcW/XR8rUQBuXl4DclHgrrbwTFLBzln8Xru5omwLpQ.', '600901234', 'pablo.diaz@example.com'), -- contraseña pass9
('Elena Ruiz', '$2b$12$AEBAw1dgaIXJsvl7UQvDIuis9ZoY6WUsMKqSHTeK131AEh7zznKqa', '600012345', 'elena.ruiz@example.com'); -- contraseña pass10



INSERT INTO producto (nombre, descripcion, precio, Foto) VALUES
('Grand Theft Auto V', 'Mundo abierto en Los Santos con misiones de crimen y caos', 29.99, 'https://upload.wikimedia.org/wikipedia/en/a/a5/Grand_Theft_Auto_V.png'),
('The Witcher 3: Wild Hunt', 'RPG épico con Geralt cazando monstruos en un mundo abierto', 39.99, 'https://upload.wikimedia.org/wikipedia/en/0/0c/Witcher_3_cover_art.jpg'),
('Red Dead Redemption 2', 'Western de mundo abierto con historia y gráficos brutales', 49.99, 'https://upload.wikimedia.org/wikipedia/en/4/44/Red_Dead_Redemption_II.jpg'),
('Cyberpunk 2077', 'RPG futurista en Night City lleno de acción y rol', 34.99, 'https://upload.wikimedia.org/wikipedia/en/9/9f/Cyberpunk_2077_box_art.jpg'),
('Elden Ring', 'Acción rol con mundo abierto y desafiantes jefes', 59.99, 'https://images.launchbox-app.com//f4cf7db9-c812-4a36-a5eb-1e9bcd6b6c37.jpg'),
('God of War', 'Kratos en mitología nórdica con combate visceral', 39.99, 'https://upload.wikimedia.org/wikipedia/en/a/a7/God_of_War_4_cover.jpg'),
('Horizon Zero Dawn', 'Cazadora contra máquinas en un mundo post-apocalíptico', 24.99, 'https://images.launchbox-app.com//18e1153b-6df2-4435-aed6-7463dd5908e7.jpg'),
('The Legend of Zelda: Breath of the Wild', 'Aventura épica en Hyrule con libertad total', 59.99, 'https://images.launchbox-app.com//9524e5f7-ccc8-4ce6-a581-03f82c51825e.jpg'),
('Super Mario Odyssey', 'Plataformas 3D con Mario viajando por mundos alocados', 49.99, 'https://upload.wikimedia.org/wikipedia/en/8/8d/Super_Mario_Odyssey.jpg'),
('Minecraft', 'Sandbox de bloques para construir y explorar', 19.99, "https://images.launchbox-app.com//bd5f9eea-510f-48d7-9356-cf95447b1d75.jpg"),
('DOOM Eternal', 'FPS vertiginoso con hordas demoníacas y armas locas', 29.99, 'https://gamesdb-images.launchbox.gg/r2_86badabe-bc26-4052-9524-cf53ab123002.jpg'),
('Sekiro: Shadows Die Twice', 'Acción samurái con parry exigente y mundo japonés', 39.99, 'https://images.launchbox-app.com//af7dd6f1-77d1-415b-9ce4-c829b26d85f8.png'),
('Dark Souls III', 'Acción rol infernal con golpes precisos y jefes desafiantes', 29.99, 'https://images.launchbox-app.com//3395b5b8-c045-4b91-9c23-efd223086848.jpg'),
('Resident Evil Village', 'Survival horror en un pueblo tétrico lleno de terror', 39.99, 'https://images.launchbox-app.com//4a9e9fc4-ada4-4f64-bcfe-ee79bd252f2d.jpg'),
('Assassin''s Creed Valhalla', 'Vikingos saqueando Inglaterra con sigilo y combate brutal', 49.99, 'https://images.launchbox-app.com//08646c03-6654-4d8b-a11c-48b5aaec39c0.png'),
('Persona 5 Royal', 'JRPG estilizado con ladrones fantasma y vida escolar', 59.99, 'https://images.launchbox-app.com//36361826-57b5-4b4c-a649-c2dbece386cb.jpg'),
('Fallout 4', 'RPG posapocalíptico en la Commonwealth de Boston', 19.99, 'https://images.launchbox-app.com//fa13979a-f44e-4253-9beb-890621b34ceb.jpg'),
('Skyrim Special Edition', 'RPG épico en mundo abierto con dragones legendarios', 29.99, 'https://images.launchbox-app.com//0316634a-65a8-4fe6-8cbd-016e89129b74.jpg'),
('Monster Hunter: World', 'Caza épica de bestias en entornos gigantes', 24.99, 'https://images.launchbox-app.com//928addf5-1db4-4041-b160-bdc0fed7ba71.jpg'),
('Borderlands 3', 'Loot-shooter con humor y cooperativo hasta 4', 39.99, 'https://images.launchbox-app.com//f8346af1-3e6b-4809-b348-3976bcf9f8c1.jpg'),
('Death Stranding', 'Aventura estrambótica de conexiones y mensajería', 29.99, 'https://images.launchbox-app.com//c22b6b40-1681-473d-a635-cf586f4e5108.jpg'),
('Metro Exodus', 'FPS post-apocalíptico con supervivencia en Rusia helada', 29.99, 'https://images.launchbox-app.com//e32ff27f-c671-4318-8f86-9fb09f2753be.jpg'),
('Disco Elysium', 'RPG narrativo con investigación detectivesca profunda', 34.99, 'https://images.launchbox-app.com//b62e64af-969b-4a79-9114-9ffa9658f10d.jpg'),
('Control', 'Acción paranormal en un edificio que cambia de forma', 19.99, 'https://images.launchbox-app.com//10bf5955-faaa-43ec-ac25-e4062d91a5d0.png'),
('Ghost of Tsushima', 'Samuráis vs mongoles en Japón feudal con estética cinematográfica', 49.99, 'https://gamesdb-images.launchbox.gg/r2_d90cb1dc-4747-4455-8914-3e2ba6ab2ee0.jpg'),
('The Last of Us Part II', 'Survival horror cinemático y emocional', 39.99, 'https://gamesdb-images.launchbox.gg/r2_562fc737-355e-4e15-a29f-ebf064e3deed.jpg'),
('Uncharted 4: A Thief''s End', 'Aventura de tesoros con Nathan Drake en alta mar', 19.99, 'https://images.launchbox-app.com//07f1ca51-57e2-4e7e-859b-fd03ebd4f7fe.jpg'),
('Marvel''s Spider-Man', 'Acción y balanceo por NYC con Peter Parker', 39.99, 'https://images.launchbox-app.com//186fa537-54da-4a7c-873d-4d18c630aec7.jpg'),
('Batman: Arkham Knight', 'Acción y sigilo como el Caballero Oscuro en Gotham', 14.99, 'https://images.launchbox-app.com//116fa30d-fbd3-4555-b5a9-ec8ef96913ee.jpg'),
('FIFA 24', 'Última entrega del simulador de fútbol con gráficos realistas', 69.99, 'https://images.launchbox-app.com//db4549e1-3208-4318-899f-564ee540f06c.jpg'),
('NBA 2K23', 'Básquetbol realista con MyCareer y modos online', 59.99, 'https://images.launchbox-app.com//1b49d70e-1834-46dc-8bff-6103067d3c30.jpg'),
('Gran Turismo 7', 'Simracing realista con coches licenciados y circuitos famosos', 59.99, 'https://images.launchbox-app.com//e3b7efd6-28ef-44db-8ca8-c722d73cbf7b.jpg'),
('Forza Horizon 5', 'Carreras arcade en México con mundo abierto vibrante', 49.99, 'https://images.launchbox-app.com//75dc9a88-9a5c-4804-bffe-8bd716d52f45.jpg'),
('Resident Evil 4', 'Remake del clásico survival horror con gráficos actuales', 39.99, 'https://i.ebayimg.com/00/s/MTM4MFgxMDAw/z/EcMAAOSwhQJkAlal/$_57.JPG?set_id=880000500F'),
('Far Cry 6', 'Shooter en isla caribeña con dictador megalómano', 49.99, 'https://images.launchbox-app.com//eebcfcc0-f1af-4ab4-8a2f-e336bb377a48.jpg'),
('Starfield', 'RPG de exploración espacial en universo abierto', 69.99, 'https://images.launchbox-app.com//c8d1ac69-8fe7-4f23-ade7-b4a81912989b.jpg'),
('God of War Ragnarök', 'Secuela de Kratos en mitología nórdica con más acción', 59.99, 'https://images.launchbox-app.com//da2f0a58-7e8f-4006-8c03-080ca92f083d.jpg'),
('Hogwarts Legacy', 'RPG de mundo abierto en el universo de Harry Potter', 59.99, 'https://images.launchbox-app.com//2cf61dcc-50d6-439a-8c5f-fa3c8a01172d.jpg'),
('Alan Wake II', 'Thriller psicológico y acción de terror', 49.99, 'https://images.launchbox-app.com//5b36bd9f-bf0f-455d-a9a8-96c3ddb566f3.jpg'),
('Dying Light 2', 'Survival horror parkour en mundo post-apocalíptico', 39.99, 'https://images.launchbox-app.com//f8d14ae3-0c31-4c4f-a8d2-6e343a1e3719.jpg'),
('A Plague Tale: Requiem', 'Aventura narrativa en la Francia de la peste', 39.99, 'https://images.launchbox-app.com//2d8b0ed0-9275-4445-ae12-83a4f5dbb49a.jpg'),
('Sifu', 'Beat ’em up con sistema de envejecimiento y kung-fu', 29.99, 'https://images.launchbox-app.com//dca3890f-a7de-4702-b1ab-2d551c4797ca.jpg'),
('Tears of the Kingdom', 'Continuación de Breath of the Wild con nuevas mecánicas', 59.99, 'https://images.launchbox-app.com//0ba7f44f-739e-4aa1-b52a-c7423fff3d1f.jpg'),
('Pikmin 4', 'Estrategia en tiempo real con simpáticos pikmins', 49.99, 'https://images.launchbox-app.com//0ba7f44f-739e-4aa1-b52a-c7423fff3d1f.jpg'),
('Mario + Rabbids Sparks of Hope', 'Estrategia táctica con personajes de Nintendo y Rabbids', 49.99, 'https://images.launchbox-app.com//3ff77894-3b20-4cb6-b904-5667a9341518.jpg'),
('Bayonetta 3', 'Hack ’n’ slash frenético con bruja poderosa', 59.99, 'https://images.launchbox-app.com//863d32b8-382b-49a1-9d99-0ef693362bad.jpg'),
('Animal Crossing: New Horizons', 'Vida isleña y recolección en paraíso tranquilo', 59.99, 'https://images.launchbox-app.com//40b87e08-ac65-42e6-9fa6-69c091dc3505.png'),
('Ratchet & Clank: Rift Apart', 'Aventura plataforma 3D con cambios de dimensión', 49.99, 'https://images.launchbox-app.com//daa79aeb-8b68-4161-b5e3-c6093cc7a227.jpg'),
('Deathloop', 'FPS de sigilo y asesinatos en bucle temporal', 54.99, 'https://images.launchbox-app.com//daa79aeb-8b68-4161-b5e3-c6093cc7a227.jpg'),
('Hollow Knight', 'Metroidvania 2D con exploración profunda y combates desafiantes', 14.99, 'https://images.launchbox-app.com//e8ee1d4e-8784-4844-8ed5-278a2527cbce.jpg');






-- Valoraciones (producto_id, cliente_id, valoracion)
INSERT INTO valoraciones (producto_id, cliente_id, valoracion) VALUES
(1, 2, 4), (1, 3, 5), (1, 4, 3), -- Grand Theft Auto V
(2, 5, 5), (2, 6, 4), (2, 7, 3), -- The Witcher 3: Wild Hunt
(3, 8, 5), (3, 9, 4), (3, 10, 5), -- Red Dead Redemption 2
(4, 11, 3), (4, 2, 4), (4, 3, 5), -- Cyberpunk 2077
(5, 4, 5), (5, 5, 4), (5, 6, 3), -- Elden Ring
(6, 7, 5), (6, 8, 4), (6, 9, 3), -- God of War
(7, 10, 5), (7, 11, 4), (7, 2, 3), -- Horizon Zero Dawn
(8, 3, 5), (8, 4, 4), (8, 5, 3), -- The Legend of Zelda: Breath of the Wild
(9, 6, 5), (9, 7, 4), (9, 8, 3), -- Super Mario Odyssey
(10, 9, 5), (10, 10, 4), (10, 11, 3), -- Minecraft
(11, 2, 5), (11, 3, 4), (11, 4, 3), -- DOOM Eternal
(12, 5, 5), (12, 6, 4), (12, 7, 3), -- Sekiro: Shadows Die Twice
(13, 8, 5), (13, 9, 4), (13, 10, 3), -- Dark Souls III
(14, 11, 5), (14, 2, 4), (14, 3, 3), -- Resident Evil Village
(15, 4, 5), (15, 5, 4), (15, 6, 3), -- Assassin's Creed Valhalla
(16, 7, 5), (16, 8, 4), (16, 9, 3), -- Persona 5 Royal
(17, 10, 5), (17, 11, 4), (17, 2, 3), -- Fallout 4
(18, 3, 5), (18, 4, 4), (18, 5, 3), -- Skyrim Special Edition
(19, 6, 5), (19, 7, 4), (19, 8, 3), -- Monster Hunter: World
(20, 9, 5), (20, 10, 4), (20, 11, 3), -- Borderlands 3
(21, 2, 5), (21, 3, 4), (21, 4, 3), -- Death Stranding
(22, 5, 5), (22, 6, 4), (22, 7, 3), -- Metro Exodus
(23, 8, 5), (23, 9, 4), (23, 10, 3), -- Disco Elysium
(24, 11, 5), (24, 2, 4), (24, 3, 3), -- Control
(25, 4, 5), (25, 5, 4), (25, 6, 3), -- Ghost of Tsushima
(26, 7, 5), (26, 8, 4), (26, 9, 3), -- The Last of Us Part II
(27, 10, 5), (27, 11, 4), (27, 2, 3), -- Uncharted 4: A Thief's End
(28, 3, 5), (28, 4, 4), (28, 5, 3), -- Marvel's Spider-Man
(29, 6, 5), (29, 7, 4), (29, 8, 3), -- Batman: Arkham Knight
(30, 9, 5), (30, 10, 4), (30, 11, 3), -- FIFA 24
(31, 2, 5), (31, 3, 4), (31, 4, 3), -- NBA 2K23
(32, 5, 5), (32, 6, 4), (32, 7, 3), -- Gran Turismo 7
(33, 8, 5), (33, 9, 4), (33, 10, 3), -- Forza Horizon 5
(34, 11, 5), (34, 2, 4), (34, 3, 3), -- Resident Evil 4
(35, 4, 5), (35, 5, 4), (35, 6, 3), -- Far Cry 6
(36, 7, 5), (36, 8, 4), (36, 9, 3), -- Starfield
(37, 10, 5), (37, 11, 4), (37, 2, 3), -- God of War Ragnarök
(38, 3, 5), (38, 4, 4), (38, 5, 3), -- Hogwarts Legacy
(39, 6, 5), (39, 7, 4), (39, 8, 3), -- Alan Wake II
(40, 9, 5), (40, 10, 4), (40, 11, 3), -- Dying Light 2
(41, 2, 5), (41, 3, 4), (41, 4, 3), -- A Plague Tale: Requiem
(42, 5, 5), (42, 6, 4), (42, 7, 3), -- Sifu
(43, 8, 5), (43, 9, 4), (43, 10, 3), -- Tears of the Kingdom
(44, 11, 5), (44, 2, 4), (44, 3, 3), -- Pikmin 4
(45, 4, 5), (45, 5, 4), (45, 6, 3), -- Mario + Rabbids Sparks of Hope
(46, 7, 5), (46, 8, 4), (46, 9, 3), -- Bayonetta 3
(47, 10, 5), (47, 11, 4), (47, 2, 3), -- Animal Crossing: New Horizons
(48, 3, 5), (48, 4, 4), (48, 5, 3), -- Ratchet & Clank: Rift Apart
(49, 6, 5), (49, 7, 4), (49, 8, 3), -- Deathloop
(50, 9, 5), (50, 10, 4), (50, 11, 3); -- Hollow Knight



-- resena (producto_id, cliente_id, resena)
INSERT INTO resena (producto_id, cliente_id, resena) VALUES
-- Grand Theft Auto V
(1, 2, 'La historia es increíble y llena de giros inesperados.'), 
(1, 3, 'El mundo abierto es muy inmersivo y detallado.'), 
(1, 4, 'Los gráficos son impresionantes para su época.'), 
(1, 5, 'El modo online es muy divertido con amigos.'), 
(1, 6, 'Las misiones secundarias son muy entretenidas.'),

-- The Witcher 3: Wild Hunt
(2, 3, 'Un RPG épico con una narrativa profunda.'), 
(2, 4, 'Los personajes están muy bien desarrollados.'), 
(2, 5, 'El combate es fluido y desafiante.'), 
(2, 6, 'El mundo abierto es hermoso y lleno de vida.'), 
(2, 7, 'La música es espectacular y muy inmersiva.'),

-- Red Dead Redemption 2
(3, 4, 'Un western con una historia increíble y emotiva.'), 
(3, 5, 'Los gráficos son de otro nivel, muy realistas.'), 
(3, 6, 'El mundo abierto está lleno de detalles impresionantes.'), 
(3, 7, 'La jugabilidad es muy realista y envolvente.'), 
(3, 8, 'Las misiones son variadas y bien diseñadas.'),

-- Cyberpunk 2077
(4, 5, 'Un juego futurista con un diseño de mundo impresionante.'), 
(4, 6, 'La historia es muy interesante y llena de sorpresas.'), 
(4, 7, 'El combate es dinámico y emocionante.'), 
(4, 8, 'El diseño de Night City es impresionante y detallado.'), 
(4, 9, 'Los personajes son muy carismáticos y memorables.'),

-- Elden Ring
(5, 6, 'Un juego desafiante pero muy gratificante.'), 
(5, 7, 'Los jefes son épicos y bien diseñados.'), 
(5, 8, 'El mundo abierto es misterioso y lleno de secretos.'), 
(5, 9, 'El diseño artístico es impresionante y único.'), 
(5, 10, 'La música es muy inmersiva y épica.'),

-- God of War
(6, 7, 'Una experiencia brutal con una gran narrativa.'), 
(6, 8, 'El combate es satisfactorio y bien diseñado.'), 
(6, 9, 'Los gráficos son espectaculares y detallados.'), 
(6, 10, 'La historia es emotiva y bien contada.'), 
(6, 11, 'El diseño de niveles es excelente y variado.'),

-- Horizon Zero Dawn
(7, 8, 'Un mundo abierto lleno de vida y detalles impresionantes.'), 
(7, 9, 'El combate es divertido y estratégico.'), 
(7, 10, 'La narrativa es interesante y bien desarrollada.'), 
(7, 11, 'Los gráficos son impresionantes y coloridos.'), 
(7, 2, 'Un juego que recomiendo a todos los amantes de la ciencia ficción.'),

-- The Legend of Zelda: Breath of the Wild
(8, 9, 'Un clásico moderno con una jugabilidad innovadora.'), 
(8, 10, 'El diseño del mundo es espectacular y lleno de secretos.'), 
(8, 11, 'La música es relajante y muy bien compuesta.'), 
(8, 2, 'Un juego que marcó una generación de jugadores.'), 
(8, 3, 'La libertad de exploración es increíble y única.'),

-- Super Mario Odyssey
(9, 10, 'Un juego muy divertido para todas las edades.'), 
(9, 11, 'El diseño de niveles es excelente y creativo.'), 
(9, 2, 'La música es pegadiza y memorable.'), 
(9, 3, 'Los gráficos son coloridos y encantadores.'), 
(9, 4, 'La jugabilidad es intuitiva y muy divertida.'),

-- Minecraft
(10, 11, 'Un sandbox que nunca pasa de moda.'), 
(10, 2, 'La creatividad no tiene límites en este juego.'), 
(10, 3, 'El modo supervivencia es muy adictivo.'), 
(10, 4, 'Los gráficos simples tienen su propio encanto.'), 
(10, 5, 'El modo multijugador es muy divertido y social.'),

-- DOOM Eternal
(11, 2, 'Un FPS frenético con mucha acción y adrenalina.'), 
(11, 3, 'La banda sonora es espectacular y motivadora.'), 
(11, 4, 'El diseño de niveles es muy creativo y desafiante.'), 
(11, 5, 'El combate es satisfactorio y emocionante.'), 
(11, 6, 'Los gráficos son impresionantes y detallados.'),

-- Sekiro: Shadows Die Twice
(12, 3, 'Un juego desafiante pero muy gratificante.'), 
(12, 4, 'El sistema de combate es único y estratégico.'), 
(12, 5, 'La ambientación japonesa es increíble y auténtica.'), 
(12, 6, 'Los jefes son memorables y bien diseñados.'), 
(12, 7, 'La historia es interesante y bien contada.'),

-- Dark Souls III
(13, 4, 'Un juego que pone a prueba tus habilidades al máximo.'), 
(13, 5, 'El diseño de niveles es excelente y bien conectado.'), 
(13, 6, 'La música es épica y memorable.'), 
(13, 7, 'Los gráficos son impresionantes y detallados.'), 
(13, 8, 'El combate es satisfactorio y desafiante.'),

-- Resident Evil Village
(14, 5, 'Un survival horror con una gran atmósfera.'), 
(14, 6, 'Los gráficos son espectaculares y realistas.'), 
(14, 7, 'La historia es interesante y llena de giros.'), 
(14, 8, 'El diseño de los enemigos es muy creativo.'), 
(14, 9, 'La jugabilidad es fluida y emocionante.'),

-- Assassin's Creed Valhalla
(15, 6, 'Un juego con una gran ambientación vikinga.'), 
(15, 7, 'El combate es divertido y variado.'), 
(15, 8, 'El mundo abierto es enorme y lleno de actividades.'), 
(15, 9, 'La historia es interesante y bien desarrollada.'), 
(15, 10, 'Los gráficos son impresionantes y detallados.'),

-- Persona 5 Royal
(16, 7, 'Un JRPG con mucho estilo y personalidad.'), 
(16, 8, 'Los personajes están muy bien desarrollados y son memorables.'), 
(16, 9, 'La música es espectacular y muy pegadiza.'), 
(16, 10, 'La historia es interesante y llena de sorpresas.'), 
(16, 11, 'La jugabilidad es adictiva y bien diseñada.'),

-- Fallout 4
(17, 8, 'Un RPG con un mundo abierto muy detallado.'), 
(17, 9, 'La personalización de armas es muy divertida.'), 
(17, 10, 'La historia es interesante y bien contada.'), 
(17, 11, 'Los gráficos son impresionantes y detallados.'), 
(17, 2, 'El sistema de combate es fluido y emocionante.'),

-- Skyrim Special Edition
(18, 9, 'Un clásico que nunca pasa de moda.'), 
(18, 10, 'El mundo abierto es enorme y lleno de vida.'), 
(18, 11, 'La música es épica y memorable.'), 
(18, 2, 'La jugabilidad es fluida y bien diseñada.'), 
(18, 3, 'Los gráficos son impresionantes y detallados.'),

-- Monster Hunter: World
(19, 10, 'Un juego muy divertido para jugar con amigos.'), 
(19, 11, 'El diseño de los monstruos es increíble y creativo.'), 
(19, 2, 'La jugabilidad es adictiva y emocionante.'), 
(19, 3, 'Los gráficos son impresionantes y detallados.'), 
(19, 4, 'La música es épica y muy inmersiva.');

INSERT INTO plataforma_juegos (producto_id, plataforma_id) VALUES
(1, 1), (1, 2), (1, 3), -- Grand Theft Auto V -> PC, PS4, PS5
(2, 1), (2, 2), (2, 3), -- The Witcher 3: Wild Hunt -> PC, PS4, PS5
(3, 1), (3, 2), (3, 3), -- Red Dead Redemption 2 -> PC, PS4, PS5
(4, 1), (4, 2), (4, 5), -- Cyberpunk 2077 -> PC, PS4, Xbox Series X/S
(5, 1), (5, 2), (5, 3), -- Elden Ring -> PC, PS4, PS5
(6, 2), (6, 3), -- God of War -> PS4, PS5
(7, 1), (7, 2), -- Horizon Zero Dawn -> PC, PS4
(8, 4), -- The Legend of Zelda: Breath of the Wild -> Nintendo Switch
(9, 4), -- Super Mario Odyssey -> Nintendo Switch
(10, 1), (10, 4), -- Minecraft -> PC, Nintendo Switch
(11, 1), (11, 2), (11, 3), -- DOOM Eternal -> PC, PS4, PS5
(12, 1), (12, 2), (12, 3), -- Sekiro: Shadows Die Twice -> PC, PS4, PS5
(13, 1), (13, 2), (13, 3), -- Dark Souls III -> PC, PS4, PS5
(14, 1), (14, 2), (14, 5), -- Resident Evil Village -> PC, PS4, Xbox Series X/S
(15, 1), (15, 2), (15, 5), -- Assassin's Creed Valhalla -> PC, PS4, Xbox Series X/S
(16, 2), -- Persona 5 Royal -> PS4
(17, 1), (17, 2), (17, 3), -- Fallout 4 -> PC, PS4, PS5
(18, 1), (18, 2), (18, 3), -- Skyrim Special Edition -> PC, PS4, PS5
(19, 1), (19, 2), (19, 3), -- Monster Hunter: World -> PC, PS4, PS5
(20, 1), (20, 2), (20, 3), -- Borderlands 3 -> PC, PS4, PS5
(21, 1), (21, 2), -- Death Stranding -> PC, PS4
(22, 1), (22, 2), (22, 3), -- Metro Exodus -> PC, PS4, PS5
(23, 1), -- Disco Elysium -> PC
(24, 1), (24, 2), (24, 3), -- Control -> PC, PS4, PS5
(25, 2), -- Ghost of Tsushima -> PS4
(26, 2), -- The Last of Us Part II -> PS4
(27, 2), -- Uncharted 4: A Thief's End -> PS4
(28, 2), -- Marvel's Spider-Man -> PS4
(29, 2), (29, 3), -- Batman: Arkham Knight -> PS4, PS5
(30, 1), (30, 2), (30, 5), -- FIFA 24 -> PC, PS4, Xbox Series X/S
(31, 1), (31, 2), (31, 5), -- NBA 2K23 -> PC, PS4, Xbox Series X/S
(32, 3), -- Gran Turismo 7 -> PS5
(33, 5), -- Forza Horizon 5 -> Xbox Series X/S
(34, 1), (34, 2), (34, 5), -- Resident Evil 4 -> PC, PS4, Xbox Series X/S
(35, 1), (35, 2), (35, 5), -- Far Cry 6 -> PC, PS4, Xbox Series X/S
(36, 1), (36, 5), -- Starfield -> PC, Xbox Series X/S
(37, 2), -- God of War Ragnarök -> PS4
(38, 1), (38, 2), (38, 5), -- Hogwarts Legacy -> PC, PS4, Xbox Series X/S
(39, 1), (39, 2), (39, 5), -- Alan Wake II -> PC, PS4, Xbox Series X/S
(40, 1), (40, 2), (40, 5), -- Dying Light 2 -> PC, PS4, Xbox Series X/S
(41, 1), (41, 2), (41, 5), -- A Plague Tale: Requiem -> PC, PS4, Xbox Series X/S
(42, 1), (42, 2), -- Sifu -> PC, PS4
(43, 4), -- Tears of the Kingdom -> Nintendo Switch
(44, 4), -- Pikmin 4 -> Nintendo Switch
(45, 4), -- Mario + Rabbids Sparks of Hope -> Nintendo Switch
(46, 4), -- Bayonetta 3 -> Nintendo Switch
(47, 4), -- Animal Crossing: New Horizons -> Nintendo Switch
(48, 1), (48, 2), -- Ratchet & Clank: Rift Apart -> PC, PS4
(49, 1), (49, 2), -- Deathloop -> PC, PS4
(50, 1), (50, 4); -- Hollow Knight -> PC, Nintendo Switch


INSERT INTO genero_juegos (producto_id, genero_id) VALUES
(1, 1), (1, 2), -- Grand Theft Auto V -> Aventura, Acción
(2, 3), (2, 1), -- The Witcher 3: Wild Hunt -> RPG, Aventura
(3, 1), (3, 2), -- Red Dead Redemption 2 -> Aventura, Acción
(4, 3), (4, 2), -- Cyberpunk 2077 -> RPG, Acción
(5, 3), (5, 1), -- Elden Ring -> RPG, Aventura
(6, 2), (6, 1), -- God of War -> Acción, Aventura
(7, 1), (7, 3), -- Horizon Zero Dawn -> Aventura, RPG
(8, 1), (8, 3), -- The Legend of Zelda: Breath of the Wild -> Aventura, RPG
(9, 1), -- Super Mario Odyssey -> Aventura
(10, 5), (10, 9), -- Minecraft -> Simulación, Multijugador
(11, 11), -- DOOM Eternal -> Shooter
(12, 2), (12, 1), -- Sekiro: Shadows Die Twice -> Acción, Aventura
(13, 3), -- Dark Souls III -> RPG
(14, 6), (14, 1), -- Resident Evil Village -> Terror, Aventura
(15, 1), (15, 2), -- Assassin's Creed Valhalla -> Aventura, Acción
(16, 3), -- Persona 5 Royal -> RPG
(17, 3), (17, 1), -- Fallout 4 -> RPG, Aventura
(18, 3), (18, 1), -- Skyrim Special Edition -> RPG, Aventura
(19, 3), (19, 2), -- Monster Hunter: World -> RPG, Acción
(20, 11), (20, 9), -- Borderlands 3 -> Shooter, Multijugador
(21, 1), (21, 2), -- Death Stranding -> Aventura, Acción
(22, 11), (22, 6), -- Metro Exodus -> Shooter, Terror
(23, 3), (23, 10), -- Disco Elysium -> RPG, Rol
(24, 2), (24, 6), -- Control -> Acción, Terror
(25, 1), (25, 2), -- Ghost of Tsushima -> Aventura, Acción
(26, 1), (26, 6), -- The Last of Us Part II -> Aventura, Terror
(27, 1), -- Uncharted 4: A Thief's End -> Aventura
(28, 2), -- Marvel's Spider-Man -> Acción
(29, 2), -- Batman: Arkham Knight -> Acción
(30, 4), -- FIFA 24 -> Deportes
(31, 4), -- NBA 2K23 -> Deportes
(32, 13), -- Gran Turismo 7 -> Carreras
(33, 13), -- Forza Horizon 5 -> Carreras
(34, 6), -- Resident Evil 4 -> Terror
(35, 11), -- Far Cry 6 -> Shooter
(36, 3), -- Starfield -> RPG
(37, 2), -- God of War Ragnarök -> Acción
(38, 3), -- Hogwarts Legacy -> RPG
(39, 6), -- Alan Wake II -> Terror
(40, 6), -- Dying Light 2 -> Terror
(41, 1), -- A Plague Tale: Requiem -> Aventura
(42, 8), -- Sifu -> Lucha
(43, 1), -- Tears of the Kingdom -> Aventura
(44, 7), -- Pikmin 4 -> Estrategia
(45, 7), -- Mario + Rabbids Sparks of Hope -> Estrategia
(46, 2), -- Bayonetta 3 -> Acción
(47, 5), -- Animal Crossing: New Horizons -> Simulación
(48, 1), (48, 2), -- Ratchet & Clank: Rift Apart -> Aventura, Acción
(49, 11), (49, 2), -- Deathloop -> Shooter, Acción
(50, 1), (50, 14); -- Hollow Knight -> Aventura, Indie