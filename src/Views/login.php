<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>

    <h2>Iniciar sesión</h2>

    <form action="login_handler.php" method="POST">
        <input type="text" id="username" name="username" placeholder="Usuario" required><br>
        <input type="password" id="password" name="password" placeholder="Contraseña" required><br><br>

        <button type="submit">Iniciar sesión</button>
    </form>

    <div id = 'enlaces'>
        <a href="password_reset.php">Recuperar contraseña</a><br>
        <a href="register.php?type=user">Crear cuenta como usuario</a>
        <a href="register.php?ytpe=admin">Crear cuenta como gimnasio</a>
    </div>

</body>
</html>