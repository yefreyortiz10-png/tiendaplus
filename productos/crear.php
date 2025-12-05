<?php
include "../config/conexion.php";

if ($_POST) {
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $cantidad = $_POST["cantidad"];
    $id_categoria = $_POST["id_categoria"];

    $query = $conexion->prepare("
        INSERT INTO productos(nombre, precio, cantidad, id_categoria)
        VALUES (:n, :p, :c, :cat)
    ");

    $query->execute([
        ":n" => $nombre,
        ":p" => $precio,
        ":c" => $cantidad,
        ":cat" => $id_categoria
    ]);

    header("Location: listar.php");
    exit();
}

$categorias = $conexion->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear Producto - TiendaPlus</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #b3ffa0ff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        width: 400px;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
    }

    input, select {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        border: none;
        border-radius: 8px;
        background: #6b73ff;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #5a62e0;
    }

    a {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #6b73ff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="form-container">
    <h1>Crear Producto</h1>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required>
        <input type="number" name="cantidad" placeholder="Cantidad disponible" required>

        <select name="id_categoria" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <button>Guardar</button>
        <a href="listar.php">⬅ Volver al listado de productos</a>
    </form>
</div>

</body>
</html>
