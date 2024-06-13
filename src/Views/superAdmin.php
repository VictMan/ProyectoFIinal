<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <title>Vista SuperAdmin</title>
    <style>
        /* Estilos adicionales para la tabla y el modal */
        th {
            <?php if (isset($_COOKIE['headerColor'])): ?>
                background-color: <?php echo $_COOKIE['headerColor'] ?>;
            <?php endif; ?>
        }

        #deleteModal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        #modalContent {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 300px;
            text-align: center;
            border-radius: 10px;
        }

        .close, #confirmDelete, #cancelDelete {
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .close {
            background-color: #aaa;
        }

        #confirmDelete {
            background-color: #4CAF50;
        }

        #cancelDelete {
            background-color: #f44336;
        }

        #clubTableContainer {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
        }

        table {
            width: 70%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        th, td {
            border-bottom: 1px solid #ddd;
            padding: 10px 8px;
            text-align: left;
        }

        .delete-button {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['username']) || $_SESSION['type'] !== 'superadmin') {
        header('Location: login.php');
        exit();
    }
    include_once('../../Database/conexion.php');
    $conexion = conectarBD();
    include_once('../includes/cabecera.php');
    ?>

    <main>
        <div id="clubTableContainer">
            <h2>Administrar Clubs</h2>
            <table id="clubTable">
                <thead>
                    <tr>
                        <th>Propietario</th>
                        <th>Nombre del Club</th>
                        <th>Número de Socios</th>
                        <th>Borrar Club</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT c.Nombre, c.Usuario, COUNT(s.Usuario) AS NumSocios 
                            FROM Club c 
                            LEFT JOIN Socio s ON c.Nombre = s.Club 
                            GROUP BY c.Nombre, c.Usuario";
                    $result = $conexion->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $clubName = $row['Nombre'];
                            $owner = $row['Usuario'];
                            $numSocios = $row['NumSocios'];
                            echo "<tr data-club='$clubName'>
                                    <td>$owner</td>
                                    <td>$clubName</td>
                                    <td>$numSocios</td>
                                    <td>
                                        <span class='delete-button' data-club='$clubName'><i class='fa-solid fa-trash'></i></span>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No hay clubs registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div id="deleteModal" class="modal">
            <div class="modal-content" id="modalContent">
                <span id="modalText">¿Estás seguro de que deseas borrar este club y todos sus socios?</span><br><br>
                <button class="confirm" id="confirmDelete">Confirmar</button>
                <button class="cancel" id="cancelDelete">Cancelar</button>
            </div>
        </div>
    </main>

    <?php include_once('../includes/pie.html'); ?>

    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                let clubName = this.getAttribute('data-club');
                document.getElementById('deleteModal').style.display = 'block';
                document.getElementById('confirmDelete').setAttribute('data-club', clubName);
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function () {
            let clubName = this.getAttribute('data-club');
            fetch('../instruments/borrar_club.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ clubName: clubName }).toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let row = document.querySelector(`tr[data-club='${clubName}']`);
                    row.remove();
                } else {
                    alert('Error al borrar el club: ' + data.message);
                }
                document.getElementById('deleteModal').style.display = 'none';
            });
        });

        document.getElementById('cancelDelete').addEventListener('click', function () {
            document.getElementById('deleteModal').style.display = 'none';
        });

        window.onclick = function(event) {
            let modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
