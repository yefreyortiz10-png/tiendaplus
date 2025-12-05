<?php
include "config/conexion.php";

if ($_POST) {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $rol = $_POST["rol"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $query = $conexion->prepare("
        INSERT INTO usuarios (nombre, email, password, rol)
        VALUES (:n, :e, :p, :r)
    ");

    $query->execute([
        ":n" => $nombre,
        ":e" => $email,
        ":p" => $password,
        ":r" => $rol
    ]);

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro - TiendaPlus</title>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6b73ff, #8df573ff);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .register-container {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        width: 350px;
        text-align: center;
    }

    .register-container h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .register-container input,
    .register-container select {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
    }

    .register-container button {
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

    .register-container button:hover {
        background: #9398ddff;
    }

    .register-container a {
        display: block;
        margin-top: 15px;
        color: #6b73ff;
        text-decoration: none;
        font-size: 14px;
    }

    .register-container a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="register-container">
    <h2>Crear Cuenta</h2>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="rol" required>
            <option value="">Selecciona un rol</option>
            <option value="cliente">Cliente</option>
            <option value="administrador">Administrador</option>
        </select>
        <button>Registrarme</button>
        <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
    </form>
</div>

</body>
</html>
