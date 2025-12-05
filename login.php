<?php
session_start();
include "config/conexion.php";

if ($_POST) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = $conexion->prepare("SELECT * FROM usuarios WHERE email = :e");
    $query->execute([":e" => $email]);
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario["password"])) {

        $_SESSION["id_usuario"] = $usuario["id"];
        $_SESSION["rol"] = $usuario["rol"];   // ✅ ESTA LÍNEA ES LA CLAVE

        header("Location: index.php");
        exit();

    } else {
        echo "Correo o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar Sesión - TiendaPlus</title>
<style>
    /* Fondo y tipografía */
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6b73ff, #8df573ff);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    /* Contenedor del formulario */
    .login-container {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        width: 350px;
        text-align: center;
    }

    .login-container h2 {
        margin-bottom: 20px;
        color: #333;
    }

    /* Inputs */
    .login-container input[type="email"],
    .login-container input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
    }

    /* Botón */
    .login-container button {
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

    .login-container button:hover {
        background: #5a62e0;
    }

    /* Mensaje de error */
    .error {
        color: #d8000c;
        background-color: #ffd2d2;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        font-size: 14px;
    }
</style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)) echo "<div class='error'>{$error}</div>"; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button>Entrar</button>
    </form>
</div>

</body>
</html>
