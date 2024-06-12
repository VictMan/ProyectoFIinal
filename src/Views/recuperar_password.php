<?php
session_start();
$error_message = "";
include_once ('../../Database/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexion = conectarBD();
    $email = $_POST['email'];

    $sql_socio = "SELECT * FROM Socio WHERE email = ?";
    $stmt_socio = $conexion->prepare($sql_socio);
    $stmt_socio->bind_param('s', $email);
    $stmt_socio->execute();
    $result_socio = $stmt_socio->get_result();

    if ($result_socio->num_rows > 0) {
        $user_found = true;
    } else {
        // Comprobar si es un club
        $sql_club = "SELECT * FROM Club WHERE email = ?";
        $stmt_club = $conexion->prepare($sql_club);
        $stmt_club->bind_param('s', $email);
        $stmt_club->execute();
        $result_club = $stmt_club->get_result();

        if ($result_club->num_rows > 0) {
            $user_found = true;
        } else {
            $user_found = false;
        }
    }

    if ($user_found) {
        // Enviar el correo electrónico con el enlace de restablecimiento de contraseña
        $reset_link = "http://localhost/php_ES_Victor/ProyectoFinal/src/Views/restablecer_password.php";
        $subject = "Restablecer Contraseña";
        $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: $reset_link";
        $headers = "From: cashclubcontrol@gmail.com";

        if (mail($email, $subject, $message, $headers)) {
            $error_message = "Se ha enviado un enlace de restablecimiento de contraseña a tu correo electrónico.";
        } else {
            $error_message = "Error al enviar el correo electrónico.";
        }
    } else {
        $error_message = "El correo electrónico o el nombre de usuario no son correctos.";
    }

    desconectarBD($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <title>Recuperar Contraseña</title>
</head>

<body>
    <?php
    include_once ('../includes/cabecera.php');
    ?>
    <main>
        <form class="registro" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <label for="email">Correo Electrónico:</label><br>
            <input type="email" id="email" name="email" placeholder="Correo Electrónico" required><br>
            <span style="color: red;"><?php echo $error_message; ?></span><br>
            <button class="inicioSesion" type="submit">Enviar</button>
        </form>
        <div id="boton_atras"><a href="login.php">Volver</a></div>
    </main>
</body>

</html>