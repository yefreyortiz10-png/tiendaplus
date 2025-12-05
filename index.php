<?php
session_start();
include "config/conexion.php";

$stmt = $conexion->query("SELECT p.id, p.nombre, p.precio, p.cantidad, c.nombre AS categoria
                          FROM productos p
                          LEFT JOIN categorias c ON p.id_categoria = c.id
                          ORDER BY p.id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usuario_id = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

if (isset($_GET['logout']) && $_GET['logout']==1) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>TiendaPlus - Catálogo</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
        }

        header {
            background: linear-gradient(135deg, #6b73ff, #8df573ff);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        header h1 {
            margin: 0;
        }

        header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        nav .btn {
            color: white;
            border: 1px solid white;
            margin-left: 8px;
        }

        nav .btn:hover {
            background: white;
            color: #000dff;
        }

        .btn {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid #333;
            background: transparent;
            transition: 0.3s;
            font-size: 14px;
            cursor: pointer;
        }

        main {
            padding: 30px;
            max-width: 1200px;
            margin: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            margin: 6px 0;
            font-size: 14px;
        }

        .precio {
            font-size: 18px;
            font-weight: bold;
            color: #000dff;
            margin-top: 10px;
        }

        .agotado {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        .card .btn {
            margin-top: 12px;
            background: #6b73ff;
            color: white;
            border: none;
            text-align: center;
        }

        .card .btn:hover {
            background: #000dff;
        }

        footer {
            text-align: center;
            padding: 20px;
            background: #111;
            color: white;
            margin-top: 40px;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<header>
    <div>
        <h1>TiendaPlus</h1>
        <p>Catálogo de Productos</p>
    </div>

    <nav>
    <?php if ($usuario_id): ?>

        <?php if ($_SESSION['rol'] == 'cliente'): ?>
            <a class="btn" href="carrito/ver.php">Mi Carrito</a>
        <?php endif; ?>

        <?php if ($_SESSION['rol'] == 'administrador'): ?>
            <a class="btn" href="admin/ventas.php">Ventas</a>
            <a class="btn" href="productos/listar.php">Administrar productos</a>
        <?php endif; ?>

        <a class="btn" href="index.php?logout=1">Cerrar sesión</a>

    <?php else: ?>

        <a class="btn" href="login.php">Ingresar</a>
        <a class="btn" href="register.php">Registrarme</a>

    <?php endif; ?>
    </nav>

</header>

<main>
    <?php if (empty($productos)): ?>
        <p>No hay productos disponibles.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($productos as $p): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($p['nombre']) ?></h3>

                    <p class="precio">$<?= number_format($p['precio'], 2, ',', '.') ?></p>

                    <p><strong>Stock:</strong> <?= (int)$p['cantidad'] ?></p>

                    <p><strong>Categoría:</strong> <?= htmlspecialchars($p['categoria'] ?? 'Sin categoría') ?></p>

                    <?php if ((int)$p['cantidad'] > 0): ?>
                        <?php if ($usuario_id && $rol === "cliente"): ?>
                            <a class="btn" href="carrito/agregar.php?id=<?= $p['id'] ?>">Agregar al carrito</a>
                        <?php elseif (!$usuario_id): ?>
                            <a class="btn" href="login.php">Inicia sesión para comprar</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="agotado">❌ Agotado</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    © <?= date("Y") ?> TiendaPlus - Todos los derechos reservados
</footer>

</body>
</html>
