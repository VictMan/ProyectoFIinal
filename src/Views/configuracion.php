<?php
session_start();
include_once('../../Database/conexion.php');

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
    if (isset($_POST['update_image'])) {
        // Verificar si se ha subido un archivo
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $target_dir = "../../Database/imagesPerfil/";
            $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["imagen"]["tmp_name"]);
            if ($check === false) {
                $error_message = "El archivo no es una imagen.";
            } elseif ($_FILES["imagen"]["size"] > 500000) {
                $error_message = "El archivo es demasiado grande.";
            } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $error_message = "Solo se permiten archivos JPG, JPEG y PNG.";
            } elseif (empty($error_message)) {
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
        } else {
            $error_message = "No se ha subido ningún archivo o ha ocurrido un error.";
        }
    } elseif (isset($_POST['update_color'])) {
        $color = $_POST['color'];
        setcookie('headerColor', $color, time() + (86400 * 30), "/"); // 30 días de duración
        $success_message = "El color del encabezado ha sido actualizado.";
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
    <style>
        <?php if(isset($_COOKIE['headerColor'])): ?>
            th {
                background-color: <?php echo $_COOKIE['headerColor']; ?>;
            }
        <?php endif; ?>
    </style>
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
                <button type="submit" name="update_image">Actualizar foto de perfil</button>
            </form>
        <?php else: ?>
            <h3>Cambiar logo</h3>
            <form action="configuracion.php" method="POST" enctype="multipart/form-data">
                <label for="imagen">Cambiar o añadir logo:</label>
                <input type="file" id="imagen" name="imagen" required><br>
                <button type="submit" name="update_image">Actualizar logo</button>
            </form>
        <?php endif; ?>

        <h3>Cambiar color del encabezado</h3>
        <form action="configuracion.php" method="POST">
            <label for="color">Seleccionar color:</label>
            <input type="color" id="color" name="color" value="<?php echo isset($_COOKIE['headerColor']) ? $_COOKIE['headerColor'] : '#ffffff'; ?>"><br>
            <button type="submit" name="update_color">Actualizar color del encabezado</button>
        </form>

        <?php if (!empty($error_message)): ?>
            <div style="color: red;"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div style="color: green;"><?php echo $success_message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
