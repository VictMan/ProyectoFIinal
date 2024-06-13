<?php
session_start();
$error_message = "";
include_once ('./Database/conexion.php');
$inlogin = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexion = conectarBD();
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Comprobar si es superadmin
    if ($user === 'superadmin' && $pass === 'superadmin') {
        $_SESSION['username'] = $user;
        $_SESSION['type'] = 'superadmin';
        desconectarBD($conexion);
        header('Location: ./src/Views/superAdmin.php');
        exit();
    }

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
            header('Location: ./src/Views/user_view.php');
            exit();
        }
    }

    // Comprobar si es un club
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
            header('Location: ./src/Views/admin_view.php');
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
    <link rel="stylesheet" href="./public/css/styles.css">
    <title>Iniciar sesión</title>
</head>
<style>

</style>

<body>
    <header>
        <div class="logo-container">
            <img src="./public/img/LogoApp.jpg" id="logo">
            <span id='appName'>CashClubControl</span>
        </div>
    </header>
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
                <a href="./src/Views/recuperar_password.php">Recuperar contraseña</a><br>
                <a href="./src/Views/register_user.php">Crear cuenta como usuario</a><br>
                <a href="./src/Views/register_admin.php">Crear cuenta como gimnasio/club</a>
            </div>
        </div>
    </main>
</body>

</html>