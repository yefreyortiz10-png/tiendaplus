<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit();
}

include "../config/conexion.php";

// Opción: aquí podrías validar rol admin si lo implementas
$stmt = $conexion->query("
    SELECT co.id, co.id_usuario, co.fecha, co.total, u.nombre AS cliente_nombre, u.email AS cliente_email
    FROM compras co
    LEFT JOIN usuarios u ON co.id_usuario = u.id
    ORDER BY co.fecha DESC
");
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ventas - TiendaPlus (Admin)</title>
    <style>
        body{font-family:Arial, margin:20px;}
        table{border-collapse:collapse;width:100%}
        th, td{padding:8px;border:1px solid #ddd;text-align:left}
        .detalle{background:#f8f8f8;font-size:14px}
        .small{font-size:0.9em;color:#666}
        a.btn{display:inline-block;padding:6px 10px;border:1px solid #333;border-radius:6px;text-decoration:none}
    </style>
</head>
<body>
    <h1>Administracion de Ventas</h1>
    <p><a class="btn" href="../index.php">Volver Atras</a></p>

    <?php if (empty($compras)): ?>
        <p>No hay ventas registradas aún.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compras as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= date("Y-m-d H:i", strtotime($c['fecha'])) ?></td>
                        <td>
                            <?= htmlspecialchars($c['cliente_nombre'] ?? 'Usuario eliminado') ?><br>
                            <span class="small"><?= htmlspecialchars($c['cliente_email'] ?? '') ?></span>
                        </td>
                        <td>$<?= number_format($c['total'],2,',','.') ?></td>
                        <td>
                            <?php
                                $detalle_stmt = $conexion->prepare("
                                    SELECT cd.*, p.nombre
                                    FROM compra_detalle cd
                                    LEFT JOIN productos p ON cd.id_producto = p.id
                                    WHERE cd.id_compra = :id_compra
                                ");
                                $detalle_stmt->execute([":id_compra" => $c['id']]);
                                $items = $detalle_stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php if (empty($items)): ?>
                                <div class="detalle">Sin detalle</div>
                            <?php else: ?>
                                <div class="detalle">
                                    <table style="width:100%;border:0;">
                                        <tr style="background:transparent;">
                                            <th style="width:50%;text-align:left">Producto</th>
                                            <th style="width:15%;text-align:right">Cantidad</th>
                                            <th style="width:20%;text-align:right">Precio unit.</th>
                                            <th style="width:15%;text-align:right">Subtotal</th>
                                        </tr>
                                        <?php foreach ($items as $it): 
                                            $subtotal = $it['cantidad'] * $it['precio'];
                                        ?>
                                        <tr style="background:transparent;">
                                            <td><?= htmlspecialchars($it['nombre'] ?? '—') ?></td>
                                            <td style="text-align:right"><?= (int)$it['cantidad'] ?></td>
                                            <td style="text-align:right">$<?= number_format($it['precio'],2,',','.') ?></td>
                                            <td style="text-align:right">$<?= number_format($subtotal,2,',','.') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
