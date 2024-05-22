<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <title>Iniciar sesión</title>
</head>

<body>

    <h2>Iniciar sesión</h2>
    <?php
    session_start();
    $error_message = "";
    include_once('../../Database/conexion.php');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       

        $user = $_POST['username'];
        $pass = md5($_POST['password']);
    
        $sql_socio = "SELECT * FROM socio WHERE username='$user' AND password='$pass'";
        $result_socio = $conn->query($sql_socio);

        if ($result_socio->num_rows > 0) {
            // Usuario es un socio
            $_SESSION['username'] = $user;
            $_SESSION['role'] = 'user';
            header('Location: user_view.php');
            exit();
        }

        $sql_club = "SELECT * FROM club WHERE username='$user' AND password='$pass'";
        $result_club = $conn->query($sql_club);

        if ($result_club->num_rows > 0) {
            $_SESSION['username'] = $user;
            $_SESSION['role'] = 'admin';
            header('Location: admin_view.php');
            exit();
        } else {
            $error_message = "Usuario o contraseña incorrectos";
        }

        $conn->close();
    }
    ?>
    <form action="login_handler.php" method="POST">
        <label>Usuario:</label><br>
        <input type="text" id="username" name="username" placeholder="Usuario" required><br>
        <label>Contraseña:</label><br>
        <input type="password" id="password" name="password" placeholder="Contraseña" required><br><br>
        <span style="color: red;"><?php echo $error_message; ?></span><br>

        <button type="submit">Iniciar sesión</button>
    </form>

    <div id='enlaces'>
        <a href="password_reset.php">Recuperar contraseña</a><br>
        <a href="register_user.php">Crear cuenta como usuario</a><br>
        <a href="register_admin.php">Crear cuenta como gimnasio/club</a>
    </div>

</body>

</html>