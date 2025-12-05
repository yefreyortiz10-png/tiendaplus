<?php
session_start();
include "../config/conexion.php";

$id_usuario = $_SESSION["id_usuario"];
$id_producto = $_GET["id"];

/* 1. Ver si el producto ya está en el carrito */
$query = $conexion->prepare("
    SELECT id, cantidad 
    FROM carrito 
    WHERE id_usuario = :u AND id_producto = :p
");
$query->execute([
    ":u" => $id_usuario,
    ":p" => $id_producto
]);

$existe = $query->fetch(PDO::FETCH_ASSOC);

/* 2. Si ya existe, aumentar la cantidad */
if ($existe) {
    $update = $conexion->prepare("
        UPDATE carrito 
        SET cantidad = cantidad + 1
        WHERE id = :id
    ");
    $update->execute([":id" => $existe["id"]]);
}

/* 3. Si no existe, insertarlo */
else {
    $insert = $conexion->prepare("
        INSERT INTO carrito(id_usuario, id_producto, cantidad)
        VALUES (:u, :p, 1)
    ");
    $insert->execute([
        ":u" => $id_usuario,
        ":p" => $id_producto
    ]);
}

/* NO redirigir al carrito */
header("Location: ../index.php");
exit();
