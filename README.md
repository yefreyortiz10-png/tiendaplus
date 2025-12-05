TiendaPlus – Sistema de Ventas Web

TiendaPlus es una aplicación web desarrollada con PHP, PostgreSQL, HTML, CSS y JavaScript, que permite la gestión de productos, usuarios, carrito de compras y ventas, con control de roles Administrador y Cliente.

## Paso 1. Preparar la base de datos en PostgreSQL

1. Crear la base de datos:
CREATE DATABASE tiendaplus;

2. Crear las tablas principales:
```sql
CREATE TABLE usuarios (
id SERIAL PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
email VARCHAR(120) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
rol VARCHAR(20) NOT NULL DEFAULT 'cliente'
);

CREATE TABLE categorias (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio NUMERIC(10,2) NOT NULL,
    cantidad INT NOT NULL,
    id_categoria INT,
    CONSTRAINT fk_categoria FOREIGN KEY(id_categoria)
    REFERENCES categorias(id)
);

CREATE TABLE carrito (
    id SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    CONSTRAINT fk_usuario FOREIGN KEY(id_usuario)
    REFERENCES usuarios(id),
    CONSTRAINT fk_producto FOREIGN KEY(id_producto)
    REFERENCES productos(id)
);

CREATE TABLE compras (
    id SERIAL PRIMARY KEY,
    id_usuario INT,
    fecha TIMESTAMP DEFAULT NOW(),
    total NUMERIC(10,2),
    CONSTRAINT fk_usuario_compra FOREIGN KEY(id_usuario)
    REFERENCES usuarios(id)
);

CREATE TABLE compra_detalle (
    id SERIAL PRIMARY KEY,
    id_compra INT,
    id_producto INT,
    cantidad INT,
    precio NUMERIC(10,2),
    CONSTRAINT fk_compra FOREIGN KEY(id_compra)
    REFERENCES compras(id),
    CONSTRAINT fk_producto_detalle FOREIGN KEY(id_producto)
    REFERENCES productos(id)
);

-- 1. Crear tipo ENUM para roles (recomendado)
CREATE TYPE rol_usuario AS ENUM ('admin', 'cliente');

-- 2. Agregar columna rol a la tabla usuarios
ALTER TABLE usuarios
ADD COLUMN rol rol_usuario NOT NULL DEFAULT 'cliente';
-----------------------------------------------
ALTER TABLE usuarios
ADD COLUMN rol VARCHAR(20) NOT NULL DEFAULT 'cliente';
```
## Paso 2. Configurar el backend (PHP + PDO + PostgreSQL)
```sql
Configurar el archivo:
config/conexion.php

$host = "localhost";
$dbname = "tiendaplus";
$user = "postgres";
$password = "yefrey10"; Tu contraseña
```
## Paso 3. Manejo de sesiones
```sql
En todos los archivos protegidos se usa:
session_start();

El sistema utiliza sesiones para:
Guardar el id_usuario
Guardar el rol (admin o cliente)
Con esta información:
El admin ve ventas y administración de productos
El cliente solo ve catálogo, carrito y compras
```
## Paso 4. Implementar la autenticación (login, registro y logout)
```sql
Archivos principales:

login.php
register.php
index.php

El registro permite seleccionar el rol:

Administrador
Cliente

El login:

Verifica contraseña con password_verify()
Guarda los datos del usuario en $_SESSION

El logout:
Destruye la sesión y redirige al catálogo
```
## Paso 5. Implementación del módulo de productos (CRUD)
```sql
Archivos principales:

productos/listar.php
productos/crear.php
productos/editar.php
productos/eliminar.php

Funciones principales:

Registrar productos
Editar productos
Eliminar productos
Listar productos por categoría
Solo el administrador tiene acceso a este módulo.
```
## Paso 6. Implementación del carrito de compras
```sql
Archivos:

carrito/agregar.php
carrito/ver.php
carrito/eliminar.php
carrito/procesar_compra.php

Funciones:

Agregar producto al carrito
Actualizar cantidad automáticamente
Ver total del carrito
Vaciar carrito al finalizar la compra
Reducir el stock al comprar
```
## Paso 7. Implementación del módulo de ventas (Administrador)
```sql
Archivo principal:

admin/ventas.php

Funciones:

Visualizar todas las compras
Ver cliente, fecha, total y detalle de productos
Acceso exclusivo para el administrador
```
## Paso 8. Separación de vistas por rol
```sql
El cliente solo puede:

Ver catálogo
Agregar al carrito
Comprar

El administrador puede:

Administrar productos
Ver ventas
Acceder a toda la plataforma
Los botones del menú cambian automáticamente según el rol.
```
## Paso 9. Ejecución del proyecto
```sql
Copiar la carpeta del proyecto dentro de:

C:\wamp64\www\


Iniciar:
PostgreSQL

Abrir en el navegador:
http://localhost/tiendaplus
```
