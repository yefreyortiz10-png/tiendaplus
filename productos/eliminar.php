<?php
include "../config/conexion.php";

$id = $_GET["id"];

try {
    $delete = $conexion->prepare("DELETE FROM productos WHERE id = :id");
    $delete->execute([":id" => $id]);

    header("Location: listar.php");
    exit();

} catch (PDOException $e) {
    echo "
    <style>
        body {
            font-family: Arial;
            background: #b3ffa0ff;
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
            width: 400px;
        }
        .error-box h2 {
            color: #d8000c;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 15px;
            background: #6b73ff;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn:hover {
            background: #5a62e0;
        }
    </style>

    <div class='error-box'>
        <h2>❌ No se puede eliminar</h2>
        <p>Este producto ya fue vendido y está registrado en el historial de compras.</p>
        <a href='listar.php' class='btn'>⬅ Volver</a>
    </div>
    ";
}
