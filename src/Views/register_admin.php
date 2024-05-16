<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <script src="../instruments/funciones.js"></script>
    <title>Crear club</title>
    <?php
    include_once ('../../Database/conexion.php');

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $clubName = isset($_POST['club']) ? $_POST['club'] : '';
    $password = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
    $nombreErr = '';
    $clubNameErr = '';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (empty($nombre)) {
            $nombreErr = "Por favor, introduce tu nombre";
        } else {
            $nombreErr = "";
        }

        if (empty($clubName)) {
            $clubNameErr = "Por favor, introduce el nombre de tu club";
        } else {
            $conexion = conectarBD();
            $sql_match = "SELECT Nombre FROM Club WHERE Nombre = '$clubName'";
            $clubNameMatch = $conexion->query($sql_match);

            if ($clubNameMatch->num_rows > 0) {
                $clubNameErr = "El nombre del club ya está en uso";
            } else{
                $clubNameErr = "";
            }
            desconectarBD($conexion);
        }

        if (empty(trim($nombreErr)) && empty(trim($clubNameErr))) {
            $conexion = conectarBD();
            $sql = "INSERT INTO club(Propietario,Nombre,Contraseña) VALUES('$nombre','$clubName','$password')";
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
    <form id = 'nuevoClub' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label>Nombre y apellido:</label><br>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre y apellido" value="<?php if (isset($_POST['nombre']))
            echo $_POST['nombre']; ?>">
        <br>
        <span class="error"><?php echo $nombreErr; ?></span>
        <br><br>

        <label>Nombre de tu club o gimnasio:</label><br>
        <input type="text" id="club" name="club" placeholder="Nombre de tu club o gimnasio" value="<?php if (isset($_POST['club']))
            echo $_POST['club']; ?>">
        <br>
        <span class="error"><?php echo $clubNameErr; ?></span>
        <br><br>

        <label>Contraseña:</label><br>
        <input type="password" id="contraseña" name="contraseña" placeholder="Contraseña">
        <br><br>

        <label>Confirma contraseña:</label><br>
        <input type="password" id="contraseña2" placeholder="Confirma contraseña">
        <span id='error-container' class='error'></span>
        <br><br>
        <input type="submit" name="entrar" id="crearClub" value="Crear">
    </form>
</body>

</html>