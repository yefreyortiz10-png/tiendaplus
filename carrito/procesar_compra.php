<?php
session_start();
include "../config/conexion.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

try {
    // Iniciar transacción
    $conexion->beginTransaction();

    // Obtener items del carrito junto con precio y stock
    $itemsStmt = $conexion->prepare("
        SELECT c.id AS carrito_id, c.id_producto, c.cantidad, p.precio, p.cantidad AS stock
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id
        WHERE c.id_usuario = :u
        FOR UPDATE
    ");
    $itemsStmt->execute([":u" => $id_usuario]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        $conexion->rollBack();
        header("Location: ver.php");
        exit();
    }

    // Verificar stock suficiente y calcular total
    $total = 0;
    foreach ($items as $it) {
        if ((int)$it['cantidad'] > (int)$it['stock']) {
            $conexion->rollBack();
            mostrarError("Stock insuficiente para alguno de los productos.");
        }
        $total += $it['precio'] * $it['cantidad'];
    }

    // Insertar compra
    $insCompra = $conexion->prepare("INSERT INTO compras (id_usuario, total) VALUES (:u, :t) RETURNING id");
    $insCompra->execute([":u" => $id_usuario, ":t" => $total]);
    $row = $insCompra->fetch(PDO::FETCH_ASSOC);
    $id_compra = $row['id'];

    // Insertar detalle y actualizar stock
    $insDetalle = $conexion->prepare("
        INSERT INTO compra_detalle (id_compra, id_producto, cantidad, precio)
        VALUES (:compra, :prod, :cant, :precio)
    ");
    $updStock = $conexion->prepare("UPDATE productos SET cantidad = cantidad - :cant WHERE id = :prod");

    foreach ($items as $it) {
        $insDetalle->execute([
            ":compra" => $id_compra,
            ":prod" => $it['id_producto'],
            ":cant" => $it['cantidad'],
            ":precio" => $it['precio']
        ]);

        $updStock->execute([
            ":cant" => $it['cantidad'],
            ":prod" => $it['id_producto']
        ]);
    }

    // Limpiar carrito
    $del = $conexion->prepare("DELETE FROM carrito WHERE id_usuario = :u");
    $del->execute([":u" => $id_usuario]);

    // Commit
    $conexion->commit();

    header("Location: ../index.php?compra=ok");
    exit();

} catch (Exception $e) {
    if ($conexion->inTransaction()) $conexion->rollBack();
    mostrarError("Error al procesar la compra: " . $e->getMessage());
}

// ✅ FUNCIÓN PARA MOSTRAR EL ERROR CON BOTÓN
function mostrarError($mensaje) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Error en la compra</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .error-box {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                text-align: center;
                width: 350px;
            }
            .error-box h2 {
                color: #d8000c;
                margin-bottom: 15px;
            }
            .error-box p {
                color: #333;
                margin-bottom: 20px;
            }
            .btn {
                display: inline-block;
                padding: 10px 15px;
                background: #6b73ff;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                transition: background 0.3s;
            }
            .btn:hover {
                background: #5a62e0;
            }
        </style>
    </head>
    <body>
        <div class='error-box'>
            <h2>❌ Compra fallida</h2>
            <p>$mensaje</p>
            <a href='ver.php' class='btn'>⬅ Volver al Carrito</a>
        </div>
    </body>
    </html>
    ";
    exit();
}
?>
