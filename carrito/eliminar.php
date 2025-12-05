<?php
session_start();
include "../config/conexion.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $del = $conexion->prepare("DELETE FROM carrito WHERE id = :id AND id_usuario = :u");
    $del->execute([":id" => $id, ":u" => $_SESSION['id_usuario']]);
}

header("Location: ver.php");
exit();
