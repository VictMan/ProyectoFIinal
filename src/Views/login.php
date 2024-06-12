<?php
session_start();
$error_message = "";
include_once ('../../Database/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexion = conectarBD();
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Comprobar si es un socio
    $sql_socio = "SELECT * FROM Socio WHERE Usuario = ?";
    $stmt_socio = $conexion->prepare($sql_socio);
    $stmt_socio->bind_param('s', $user);
    $stmt_socio->execute();
    $result_socio = $stmt_socio->get_result();

    if ($result_socio->num_rows > 0) {
        $socio = $result_socio->fetch_assoc();
        if (password_verify($pass, $socio['Contraseña'])) {
            $_SESSION['username'] = $user;
            $_SESSION['type'] = 'socio';
            desconectarBD($conexion);
            header('Location: user_view.php');
            exit();
        }
    }

    $sql_club = "SELECT * FROM Club WHERE Usuario = ?";
    $stmt_club = $conexion->prepare($sql_club);
    $stmt_club->bind_param('s', $user);
    $stmt_club->execute();
    $result_club = $stmt_club->get_result();

    if ($result_club->num_rows > 0) {
        $club = $result_club->fetch_assoc();
        if (password_verify($pass, $club['Contraseña'])) {
            $_SESSION['username'] = $user;
            $_SESSION['type'] = 'admin';
            desconectarBD($conexion);
            header('Location: admin_view.php');
            exit();
        }
    }

    $error_message = "Usuario o contraseña incorrectos";
    desconectarBD($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <title>Iniciar sesión</title>
</head>

<body>
    <?php
    include_once ('../includes/cabecera.php');
    ?>
    <main>
        <h2>Iniciar sesión</h2>
        <div class="registro">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                <label>Usuario:</label><br>
                <input type="text" id="username" name="username" placeholder="Usuario" required><br>
                <label>Contraseña:</label><br>
                <input type="password" id="password" name="password" placeholder="Contraseña" required><br><br>
                <span style="color: red;"><?php echo $error_message; ?></span><br>
                <button type="submit" class="inicioSesion">Iniciar sesión</button>
            </form>
            <div class='enlaces'>
                <a href="recuperar_password.php">Recuperar contraseña</a><br>
                <a href="register_user.php">Crear cuenta como usuario</a><br>
                <a href="register_admin.php">Crear cuenta como gimnasio/club</a>
            </div>
        </div>
    </main>
    <?php
    include_once ('../includes/pie.html');
    ?>
</body>

</html>