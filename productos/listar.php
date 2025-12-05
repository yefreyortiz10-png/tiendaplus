<?php
include "../config/conexion.php";

$query = $conexion->query("
    SELECT p.*, c.nombre AS categoria
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id
");
$productos = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Administrar Productos - TiendaPlus</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
        background: #f5f5f5;
    }

    h1 {
        color: #333;
    }

    .btn {
        display: inline-block;
        padding: 8px 12px;
        margin: 5px 0;
        border-radius: 6px;
        text-decoration: none;
        border: 1px solid #333;
        background: #6b73ff;
        color: white;
        transition: 0.3s;
    }

    .btn:hover {
        background: #5a62e0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
    }

    th {
        background: #6b73ff;
        color: white;
    }

    tr:nth-child(even) {
        background: #f2f2f2;
    }

    tr:hover {
        background: #e0e0ff;
    }

    td a {
        color: #6b73ff;
        text-decoration: none;
        font-weight: bold;
    }

    td a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<h1>Productos</h1>

<a href="crear.php" class="btn">➕ Nuevo Producto</a>
<a href="../index.php" class="btn">⬅ Volver a la página anterior</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Categoría</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($productos as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td>$<?= number_format($p['precio'], 2, ',', '.') ?></td>
        <td><?= (int)$p['cantidad'] ?></td>
        <td><?= htmlspecialchars($p['categoria'] ?? 'Sin categoría') ?></td>
        <td>
            <a href="editar.php?id=<?= $p['id'] ?>">Editar</a> |
            <a href="eliminar.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
