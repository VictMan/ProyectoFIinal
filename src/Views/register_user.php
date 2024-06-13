<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuario</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <script src="../instruments/funciones.js"></script>
    <?php
    include_once ('../../Database/conexion.php');
    include_once ('../instruments/funcionesPHP.php');

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
    $userName = isset($_POST['userName']) ? $_POST['userName'] : '';
    $email = isset($_POST['correo']) ? $_POST['correo'] : '';
    $club = isset($_POST['selectClub']) ? $_POST['selectClub'] : '';
    $nombreErr = '';
    $userNameErr = '';
    $clubErr = '';
    $emailErr = '';
    ?>
</head>

<m>
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

        if (empty($email)) {
            $emailErr = "Por favor, introduce tu correo electrónico";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Formato de correo inválido";
            } else {
                $emailErr = "";
            }
        }

        if (empty(trim($nombreErr)) && empty(trim($userNameErr)) && empty(trim($clubErr)) && empty(trim($emailErr))) {
            $conexion = conectarBD();

            $foto = '';
            if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
                $fileType = mime_content_type($_FILES['Foto']['tmp_name']);
                if (in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
                    $foto = basename($_FILES['Foto']['name']);
                    $uploadDir = '../../public/img/';
                    $uploadFile = $uploadDir . $foto;

                    if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $uploadFile)) {
                        echo "Error al subir la foto de perfil.";
                        exit();
                    }
                } else {
                    echo "Solo se permiten archivos JPG, PNG y GIF.";
                    exit();
                }
            }

            $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);

            $sql = "INSERT INTO socio (Nombre, Usuario, Contraseña, `Cuota Pagada`, `Último pago`, `Próximo pago`, Club, Foto, Email) VALUES ('$nombre', '$userName', '$hashedPassword', 'false', '', '', '$club', '$foto', '$email')";
            if ($conexion->query($sql) === true) {
                header('Location: ../../login.php');
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
                var fileInput = $('#fotoPerfil')[0];
                var file = fileInput.files[0];
                var fileErrorContainer = $('#file-error-container');

                e.preventDefault();

                if (file) {
                    var fileType = file.type;
                    var validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if ($.inArray(fileType, validImageTypes) < 0) {
                        fileErrorContainer.text('Solo se permiten archivos JPG, PNG y GIF.').show();
                        return;
                    } else {
                        fileErrorContainer.text('').hide();
                    }
                }

                if (validatePassword(contraseña, contraseña2, errorContainer)) $('#nuevoUsuario').submit();
            });
        });
    </script>

    <?php
    include_once ('../includes/cabecera.php');
    ?>
    <main>
        <form id="nuevoUsuario" class="registro" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
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

            <label>Correo electrónico:</label>
            <input type="email" id="correo" name="correo" placeholder="Email" value="<?php echo $email ?>">
            <br>
            <span class="error"><?php echo $emailErr; ?></span>
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

            <label>Foto de perfil:</label>
            <input type="file" id="fotoPerfil" name="fotoPerfil"><br>
            <span id='file-error-container' class='error'></span>
            <br><br>

            <input type="submit" name="crearUsuario" id="crearUsuario" value="Crear Perfil">
            <br>
        </form>
    </main>
    <?php
    include_once ('../includes/pie.html');
    ?>
    </body>

</html>