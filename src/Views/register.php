<?php
$type = isset($_GET['type']) ? $_GET['type'] : 'user';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>

<body>
    <?php if ($type === 'user') { ?>
        <form action="register_user.php" method="POST">
            <input type="text" id="nombre" class="nombre" placeholder="Nombre" required>
            <br><br>
            <input type="text" id="apellido" class="apellido" placeholder="Apellido" required>
            <br><br>
            <input type="email" id="correo" class="correo" placeholder="Correo electrónico" required>
            <br><br>
            <input type="text" id="nombreUser" class="nombreUser" placeholder="Nombre de Usuario" required>
            <br><br>
            <input type="password" id="contraseña" class="contraseña" placeholder="Contraseña">
            <br><br>
            <input type="password" id="contraseña2" class="contraseña2" placeholder="Confirma contraseña">
            <br><br>
        </form>
    <?php } else if ($type === 'admin') { ?>
        <form action="register_admin.php" method="POST">

        </form>
    <?php } ?>
</body>

</html>