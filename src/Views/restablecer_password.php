<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <script src="../instruments/funciones.js"></script>
    <title>Restablecer mi contraseña</title>
</head>

<body>
    <?php
    session_start();
    include_once ('../../Database/conexion.php');

    $conexion = conectarBD();
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $new_password = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    $error_message = "";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sql_socio = "SELECT * FROM Socio WHERE Usuario = ?";
        $stmt_socio = $conexion->prepare($sql_socio);
        $stmt_socio->bind_param('s', $username);
        $stmt_socio->execute();
        $result_socio = $stmt_socio->get_result();

        if ($result_socio->num_rows > 0) {
            $sql_update_socio = "UPDATE Socio SET Contraseña = ? WHERE Usuario = ?";
            $stmt_update_socio = $conexion->prepare($sql_update_socio);
            $stmt_update_socio->bind_param('ss', $hashedPassword, $username);
            if ($stmt_update_socio->execute()) {
                $error_message = "Contraseña actualizada exitosamente.";
            } else {
                $error_message = "Error al actualizar la contraseña.";
            }
        } else {
            $sql_club = "SELECT * FROM Club WHERE Usuario = ?";
            $stmt_club = $conexion->prepare($sql_club);
            $stmt_club->bind_param('s', $username);
            $stmt_club->execute();
            $result_club = $stmt_club->get_result();

            if ($result_club->num_rows > 0) {
                $sql_update_club = "UPDATE Club SET Contraseña = ? WHERE Usuario = ?";
                $stmt_update_club = $conexion->prepare($sql_update_club);
                $stmt_update_club->bind_param('ss', $hashedPassword, $username);
                if ($stmt_update_club->execute()) {
                    $error_message = "Contraseña actualizada exitosamente.";
                } else {
                    $error_message = "Error al actualizar la contraseña.";
                }
            } else {
                $error_message = "El nombre de usuario no es correcto.";
            }
        }
    }
    ?>
    <script>
        $(document).ready(function () {
            $('#comprobarPasswords').click(function (e) {
                var contraseña = $('#contraseña').val();
                var contraseña2 = $('#contraseña2').val();
                var errorContainer = $('#error-container');

                e.preventDefault();
                if (validatePassword(contraseña, contraseña2, errorContainer)) $('#restablecerPassword').submit();
            });
        });
    </script>
    <h2>Restablecer Contraseña</h2>
    <form id="restablecerPassword" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <label>Nombre de Usuario:</label><br>
        <input type="text" name="username" placeholder="Username" value="<?php echo $username ?>"> 
        <br>
        <label>Nueva Contraseña:</label><br>
        <input type="password" id="contraseña" name="contraseña" placeholder="Nueva Contraseña" required><br>
        <label>Confirmar Nueva Contraseña:</label><br>
        <input type="password" id="contraseña2" name="contraseña2" placeholder="Confirmar Nueva Contraseña"
            required><br>
        <span id="error_message" style="color: red;"><?php echo $error_message; ?></span><br><br>
        <button type="submit" id="comprobarPasswords">Restablecer Contraseña</button>
    </form>
    <a href="login.php">Iniciar sesión</a>
</body>

</html>