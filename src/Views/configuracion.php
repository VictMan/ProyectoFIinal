<?php
session_start();
include_once ('../../Database/conexion.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conexion = conectarBD();
$usuario = $_SESSION['username'];
$tipo = $_SESSION['type'];

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "../../Database/imagesPerfil/";
    $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["imagen"]["tmp_name"]);
    if ($check === false) {
        $error_message = "El archivo no es una imagen.";
    }
    if ($_FILES["imagen"]["size"] > 500000) {
        $error_message = "El archivo es demasiado grande.";
    }
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error_message = "Solo se permiten archivos JPG, JPEG y PNG.";
    }
    if (empty($error_message)) {
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            if ($tipo == 'socio') {
                $sql = "UPDATE Socio SET Foto = ? WHERE Usuario = ?";
            } else {
                $sql = "UPDATE Club SET Logo = ? WHERE Usuario = ?";
            }

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ss", $target_file, $usuario);

            if ($stmt->execute()) {
                $success_message = ($tipo == 'socio') ? "La foto de perfil ha sido actualizada con éxito." : "El logo ha sido actualizado con éxito.";
            } else {
                $error_message = "Error al actualizar la imagen.";
            }
            $stmt->close();
        } else {
            $error_message = "Error al subir el archivo.";
        }
    }
}
desconectarBD($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <title>Configuración</title>
</head>
<body>
<?php
    include_once('../includes/cabecera.php');
    ?>
    <h2>Configuración</h2>
    <div class="configuracion-content">
        <?php if ($tipo === 'socio'): ?>
            <h3>Cambiar foto de perfil</h3>
            <form action="configuracion.php" method="POST" enctype="multipart/form-data">
                <label for="imagen">Cambiar o añadir foto de perfil:</label>
                <input type="file" id="imagen" name="imagen" required><br>
                <button type="submit">Actualizar foto de perfil</button>
            </form>
        <?php else: ?>
            <h3>Cambiar logo</h3>
            <form action="configuracion.php" method="POST" enctype="multipart/form-data">
                <label for="imagen">Cambiar o añadir logo:</label>
                <input type="file" id="imagen" name="imagen" required><br>
                <button type="submit">Actualizar logo</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div style="color: red;"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div style="color: green;"><?php echo $success_message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

