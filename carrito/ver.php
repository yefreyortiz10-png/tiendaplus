<?php
session_start();
include "../config/conexion.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$query = $conexion->prepare("
SELECT c.id AS carrito_id, p.id AS producto_id, p.nombre, p.precio, c.cantidad, p.cantidad AS stock
FROM carrito c
INNER JOIN productos p ON c.id_producto = p.id
WHERE c.id_usuario = :u
");
$query->execute([":u" => $id_usuario]);
$carrito = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Mi Carrito</title>
<style>
table{border-collapse:collapse;width:100%}
th, td{padding:8px;border:1px solid #ddd;text-align:left}
.btn{padding:6px 10px;border:1px solid #333;border-radius:6px;text-decoration:none}
.small{font-size:0.9em;color:#666}
</style>
</head>
<body>
<h1>Mi Carrito</h1>
<p><a class="btn" href="../index.php">Volver al catálogo</a></p>

<?php if (empty($carrito)): ?>
    <p>Tu carrito está vacío.</p>
<?php else: ?>
    <table>
        <tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th>Acción</th></tr>
        <?php $total = 0; foreach ($carrito as $item): 
            $subtotal = $item['precio'] * $item['cantidad'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['nombre']) ?></td>
            <td>$<?= number_format($item['precio'],2,',','.') ?></td>
            <td><?= (int)$item['cantidad'] ?> (stock: <?= (int)$item['stock'] ?>)</td>
            <td>$<?= number_format($subtotal,2,',','.') ?></td>
            <td>
                <a class="btn" href="eliminar.php?id=<?= $item['carrito_id'] ?>">Quitar</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" style="text-align:right"><strong>Total:</strong></td>
            <td colspan="2"><strong>$<?= number_format($total,2,',','.') ?></strong></td>
        </tr>
    </table>

    <p>
        <a class="btn" href="procesar_compra.php">Procesar compra</a>
    </p>
<?php endif; ?>
</body>
</html>
