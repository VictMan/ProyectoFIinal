<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <?php
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellido = isset($_POST['contraseña']) ? $_POST['apellido'] : '';
    $userName = isset($_POST['userName']) ? $_POST['userName'] : '';
    $club = isset($_POST['club']) ? $_POST['club'] : '';
    $nombreErr = '';
    $userNameErr = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (empty($nombre)) {
            $nombreErr = "Por favor, introduce tu nombre";
        } else {
            $nombreErr = "";
        }

        if (empty($userName)) {
            $userNameErr = "Por favor, introduce el nombre de tu club";
        } else {
            $conexion = conectarBD();
            $sql_match = "SELECT Nombre FROM Club WHERE Nombre = '$userName'";
            $userNameMatch = $conexion->query($sql_match);

            if ($userNameMatch->num_rows > 0) {
                $userNameErr = "El nombre del club ya está en uso";
            } else{
                $userNameErr = "";
            }
            desconectarBD($conexion);
        }

        if (empty(trim($nombreErr)) && empty(trim($userNameErr))) {
            $conexion = conectarBD();
            $sql = "INSERT INTO club(Propietario,Nombre,Contraseña) VALUES('$nombre','$userName','$password')";
            if ($conexion->query($sql) === true) {
                header('Location:./login.php');
                echo "Registro insertado correctamente.";
            } else {
                echo "Error al insertar el registro: " . $conexion->error;
            }
            desconectarBD($conexion);
        }
    }
    ?>
</head>

<body>
    <script>
        $(document).ready(function () {
            $('#crearClub').click(function (e) {
                var contraseña = $('#contraseña').val();
                var contraseña2 = $('#contraseña2').val();
                var errorContainer = $('#error-container');

                e.preventDefault();

                if(validatePassword(contraseña, contraseña2, errorContainer)) $('#nuevoClub').submit();
            });
        });

    </script>

    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
        <label>Nombre y apellido:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre y apellido" value="<?php echo $nombre ?>">
        <br><br>
        <label>Nombre de Usuario:</label>
        <input type="text" id="nombreUser" name="nombreUser" placeholder="Nombre de Usuario" value="<?php echo $userName ?>">
        <br><br>
        <label>Club</label>
        <select name="club" id="club"></select>
        <label>Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" placeholder="Contraseña">
        <br><br>
        <label>Confirma contraseña:</label>
        <input type="password" id="contraseña2" name="contraseña2" placeholder="Confirma contraseña">
        <br><br>
        <input type="submit" name="entrar" value="Entrar" id="enviar">
        <br>
    </form>
</body>

</html>