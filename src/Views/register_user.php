<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuario</title>
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <script src="../instruments/funciones.js"></script>
    <?php
    include_once ('../../Database/conexion.php');
    include_once('../instruments/funcionesPHP.php');

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
    $userName = isset($_POST['userName']) ? $_POST['userName'] : '';
    $club = isset($_POST['selectClub']) ? $_POST['selectClub'] : '';
    $nombreErr = '';
    $userNameErr = '';
    $clubErr = '';
    function dibuja_select($nomSel, $tabla, $campo)
    {
        $html = "<select name='$nomSel'>\n";
        $html .= "<option value='Todos'>Todos</option>\n";
        $conexion = conectarBD();
        $sql = "SELECT DISTINCT $campo FROM $tabla ORDER BY $campo";
        $res = $conexion->query($sql);

        while ($fila = $res->fetch_array()) {
            $valor = $fila[$campo];
            $selected = isset($_POST[$nomSel]) && $_POST[$nomSel] == $valor ? "selected" : "";
            $html .= "<option value='$valor' $selected>$valor</option>\n";
        }

        $res->free();
        desconectarBD($conexion);
        $html .= "</select>\n";

        return $html;
    }
    ?>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (empty($nombre)) {
            $nombreErr = "Por favor, introduce tu nombre";
        } else {
            $nombreErr = "";
        }

        if (empty($userName)) {
            $userNameErr = "Por favor, introduce tu nombre de usuario";
        } else {
            $userNameErr = comprobarUserName($userName);
        }
        if (empty($club)) {
            $clubErr = 'Por favor, introduce el nombre de tu club';
        } else {
            $clubErr = '';
        }

        if (empty(trim($nombreErr)) && empty(trim($userNameErr)) && empty(trim($clubErr))) {
            $conexion = conectarBD();
            $sql = "INSERT INTO socio(Nombre, Usuario, Contraseña, `Cuota Pagada`, `Último pago`, `Próximo pago`, Club) VALUES('$nombre','$userName','$contraseña', 'false', '','', '$club')";
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
    <script>
        $(document).ready(function () {
            $('#crearUsuario').click(function (e) {
                var contraseña = $('#contraseña').val();
                var contraseña2 = $('#contraseña2').val();
                var errorContainer = $('#error-container');

                e.preventDefault();

                if (validatePassword(contraseña, contraseña2, errorContainer)) $('#nuevoUsuario').submit();
            });
        });

    </script>

    <form  id="nuevoUsuario" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <label>Nombre y apellido:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre y apellido" value="<?php echo $nombre ?>">
        <br>
        <span class="error"><?php echo $nombreErr; ?></span>
        <br><br>

        <label>Nombre de Usuario:</label>
        <input type="text" id="userName" name="userName" placeholder="Nombre de Usuario"
            value="<?php echo $userName ?>">
        <br>
        <span class="error"><?php echo $userNameErr; ?></span>
        <br><br>

        <p>Club: <?php echo dibuja_select("selectClub", "Club", "nombre") ?></p>
        <span class="error"><?php echo $clubErr; ?></span>

        <br><br>
        <label>Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" placeholder="Contraseña">
        <br><br>

        <label>Confirma contraseña:</label>
        <input type="password" id="contraseña2" name="contraseña2" placeholder="Confirma contraseña">
        <span id='error-container' class='error'></span>
        <br><br>

        <input type="submit" name="crearUsuario" id="crearUsuario" value="Crear Perfil" >
        <br>
    </form>
</body>

</html>