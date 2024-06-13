<?php
session_start();
include_once('../../Database/conexion.php');

if (!isset($_SESSION['username'])) {
    header('Location: ../../login.php');
    exit();
}

$conexion = conectarBD();
$usuario = $_SESSION['username'];
$tipo = $_SESSION['type'];

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_image'])) {
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
        setcookie('headerColor', $color, time() + (86400 * 365), "/");
        $success_message = "El color del encabezado ha sido actualizado.";
    } elseif (isset($_POST['update_schedule'])) {
        if (isset($_FILES["horario"]) && $_FILES["horario"]["error"] == 0) {
            $target_dir = "../../Database/horarios/";
            $target_file = $target_dir . basename($_FILES["horario"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["horario"]["tmp_name"]);
            if ($check === false) {
                $error_message = "El archivo no es una imagen.";
            } elseif ($_FILES["horario"]["size"] > 500000) {
                $error_message = "El archivo es demasiado grande.";
            } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $error_message = "Solo se permiten archivos JPG, JPEG y PNG.";
            } elseif (empty($error_message)) {
                if (move_uploaded_file($_FILES["horario"]["tmp_name"], $target_file)) {
                    $sql = "UPDATE Club SET HorarioImagen = ? WHERE Usuario = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ss", $target_file, $usuario);

                    if ($stmt->execute()) {
                        $success_message = "El horario ha sido actualizado con éxito.";
                    } else {
                        $error_message = "Error al actualizar la imagen del horario.";
                    }
                    $stmt->close();
                } else {
                    $error_message = "Error al subir el archivo del horario.";
                }
            }
        } else {
            $error_message = "No se ha subido ningún archivo o ha ocurrido un error.";
        }
    } elseif (isset($_POST['update_email'])) {
        $correo = $_POST['correo'];
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Formato de correo inválido.";
        } else {
            if ($tipo == 'socio') {
                $sql = "UPDATE Socio SET Email = ? WHERE Usuario = ?";
            } else {
                $sql = "UPDATE Club SET Email = ? WHERE Usuario = ?";
            }
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ss", $correo, $usuario);
            if ($stmt->execute()) {
                $success_message = "El correo electrónico ha sido actualizado con éxito.";
            } else {
                $error_message = "Error al actualizar el correo electrónico.";
            }
            $stmt->close();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Configuración</title>
    <style>
        <?php if(isset($_COOKIE['headerColor'])): ?>
            th {
                background-color: <?php echo $_COOKIE['headerColor']; ?>;
            }
        <?php endif; ?>
        .popup {
            padding: 50px;
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 20px;
            position: fixed;
            z-index: 1;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 17px;
        }

        .popup.show {
            visibility: visible;
        }

        .popup .close {
            position: absolute;
            top: 5px;
            right: 10px;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php
    include_once('../includes/cabecera.php');
?>
<main id="configuracionMain">
    <h2>Configuración</h2>
    <div class="configuracion-content">
        <div id = "card">
        <h3>Cambiar correo electrónico</h3>
        <form action="configuracion.php" method="POST">
            <label for="correo">Nuevo correo electrónico:</label>
            <input type="email" id="correo" name="correo" required value="<?php echo isset($_POST['correo']) ? $_POST['correo'] : ''; ?>"><br>
            <button type="submit" name="update_email">Actualizar correo electrónico</button>
        </form>
        </div>
        <div id = "card">
        <h3>Cambiar color del encabezado</h3>
        <form action="configuracion.php" method="POST">
            <label for="color">Seleccionar color:</label>
            <div id="container-color">
            <input type="color" id="color" name="color" value="<?php echo isset($_COOKIE['headerColor']) ? $_COOKIE['headerColor'] : '#ffffff'; ?>"><br>
            <button type="submit" name="update_color">Actualizar color del encabezado</button>
            </div>
        </form>
        </div>
        <?php if ($tipo === 'socio'): ?>
            <div id = "card">
            <h3>Cambiar foto de perfil</h3>
            <form action="configuracion.php" method="POST" enctype="multipart/form-data">
                <label for="imagen">Cambiar o añadir foto de perfil:</label>
                <input type="file" id="imagen" name="imagen" required><br>
                <button type="submit" name="update_image">Actualizar foto de perfil</button>
            </form>
            </div>
        <?php else: ?>
            <div id = "card">
            <h3>Cambiar logo</h3>
            <form action="configuracion.php" method="POST" enctype="multipart/form-data">
                <label for="imagen">Cambiar o añadir logo:</label>
                <input type="file" id="imagen" name="imagen" required><br>
                <button type="submit" name="update_image">Actualizar logo</button>
            </form>
            </div>

            <div id = "card">
            <h3>Subir horario del club</h3>
            <form action="configuracion.php" method="POST" enctype="multipart/form-data">
                <label for="horario">Subir o cambiar el horario:</label>
                <input type="file" id="horario" name="horario" accept="image/*" required><br>
                <button type="submit" name="update_schedule">Subir horario</button>
            </form>
            </div>
        <?php endif; ?>

        <div id="popup" class="popup">
            <span class="close" onclick="document.getElementById('popup').style.visibility='hidden';">&times;</span>
            <span id="popup_message"></span>
        </div>

        <?php if ($success_message): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var popup = document.getElementById('popup');
                    var popupMessage = document.getElementById('popup_message');
                    popupMessage.textContent = "<?php echo $success_message; ?>";
                    popup.classList.add('show');
                    setTimeout(function() {
                        popup.classList.remove('show');
                    }, 3000);
                });
            </script>
        <?php elseif ($error_message): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var popup = document.getElementById('popup');
                    var popupMessage = document.getElementById('popup_message');
                    popupMessage.textContent = "<?php echo $error_message; ?>";
                    popup.classList.add('show');
                    setTimeout(function() {
                        popup.classList.remove('show');
                    }, 3000);
                });
            </script>
        <?php endif; ?>
    </div>
</main>
<?php
    include_once('../includes/pie.html');
?>
</body>
</html>
