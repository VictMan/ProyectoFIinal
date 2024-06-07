<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <title>Vista de Administrador</title>
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
    session_start();
    if (!isset($_SESSION['username']) || $_SESSION['type'] !== 'admin') {
        header('Location: login.php');
        exit();
    }
    include_once('../includes/cabecera.php');
    include_once ('../../Database/conexion.php');
    $conexion = conectarBD();

    $adminUsername = $_SESSION['username'];
    $querySelect = "SELECT Nombre, Logo FROM Club WHERE Usuario = '$adminUsername'";
    $result = $conexion->query($querySelect);
    $nombreClub = $result->fetch_assoc();
    $miClub = $nombreClub['Nombre'];
    $logoClub = $nombreClub['Logo'];

    // Ruta del logo por defecto
    $defaultLogo = '../../Database/imagesPerfil/default_logo.jpg';

    // Verificar si existe un logo personalizado
    if (empty($logoClub) || !file_exists($logoClub)) {
        $logoClub = $defaultLogo;
    }

    echo '<h2>' . $miClub . '</h2>';
    echo '<img src="' . $logoClub . '" alt="Logo del Club" class="club-logo">';
    ?>

    <label for="filter">Mostrar:</label>
    <select id="filter" name="filter">
        <option value="all" <?php if (!isset($_GET['filter']) || $_GET['filter'] == 'all') echo 'selected'; ?>>Todos</option>
        <option value="paid" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'paid') echo 'selected'; ?>>Al corriente de pago</option>
        <option value="unpaid" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'unpaid') echo 'selected'; ?>>No han pagado</option>
    </select>

    <table border="1" id="sociosTable">
        <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Cuota</th>
            <th>Fecha del último pago</th>
            <th>Fecha del próximo pago</th>
            <th>Borrar Socio</th>
        </tr>
        <tbody id="sociosTableBody">
            <?php
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $whereClause = '';

            if ($filter === 'paid') {
                $whereClause = "AND `Cuota pagada` = 1";
            } elseif ($filter === 'unpaid') {
                $whereClause = "AND `Cuota pagada` = 0";
            }

            $sql = "SELECT Nombre, Usuario, `Cuota pagada`, `Último pago`, `Próximo pago`
                    FROM Socio
                    WHERE Club = '$miClub' $whereClause";
            $selectMiTabla = $conexion->prepare($sql);
            $selectMiTabla->execute();
            $result = $selectMiTabla->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $nombre = $row['Nombre'];
                    $usuario = $row['Usuario'];
                    $cuota_pagada = $row['Cuota pagada'];
                    $fecha_ultimo_pago = date("d-m-Y", strtotime($row['Último pago']));
                    $fecha_proximo_pago = date("d-m-Y", strtotime($row['Próximo pago']));

                    $checked = $cuota_pagada ? 'checked' : '';
                    echo "<tr data-id='$usuario'>
                            <td>$nombre</td>
                            <td>$usuario</td>
                            <td>
                                <label class='switch'>
                                    <input type='checkbox' class='cuota-switch' data-id='$usuario' $checked>
                                    <span class='slider'></span>
                                </label>
                            </td>
                            <td class='fecha-ultimo-pago' data-id='$usuario'>$fecha_ultimo_pago</td>
                            <td class='fecha-proximo-pago' data-id='$usuario'>$fecha_proximo_pago</td>
                            <td>
                                <span class='delete-button' data-id='$usuario'><i class='fa-solid fa-trash'></i></span>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay socios registrados en este club</td></tr>";
            }

            desconectarBD($conexion);
            ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span id="modalText">¿Estás seguro de que deseas borrar este socio?</span><br><br>
            <button class="confirm" id="confirmDelete">Confirmar</button>
            <button class="cancel" id="cancelDelete">Cancelar</button>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                let socioUsuario = this.getAttribute('data-id');
                document.getElementById('deleteModal').style.display = 'block';
                document.getElementById('confirmDelete').setAttribute('data-id', socioUsuario);
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function () {
            let socioUsuario = this.getAttribute('data-id');
            fetch('../instruments/borrar_socio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ socioUsuario: socioUsuario }).toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let row = document.querySelector(`tr[data-id='${socioUsuario}']`);
                    row.remove();
                } else {
                    alert('Error al borrar el socio');
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

        document.querySelectorAll('.cuota-switch').forEach(switchChange => {
            let row = switchChange.closest('tr');
            let fechaProximoPago = new Date(row.querySelector('.fecha-proximo-pago').textContent.split('-').reverse().join('-'));
            let today = new Date();

            if (today >= fechaProximoPago) {
                switchChange.checked = false;
                actualizarCuota(switchChange, false);
            }

            switchChange.addEventListener('change', function () {
                actualizarCuota(this, this.checked);
            });
        });

        function actualizarCuota(switchChange, isManualChange) {
            let row = switchChange.closest('tr');
            let socioUsuario = row.getAttribute('data-id');
            let cuotaPagada = switchChange.checked ? 1 : 0;
            let fechaUltimoPago = null;
            let fechaProximoPago = null;

            if (cuotaPagada) {
                let now = new Date();
                fechaUltimoPago = now.toISOString().split('T')[0];

                let nextMonth = new Date(now.setMonth(now.getMonth() + 1));
                let nextYear = nextMonth.getFullYear();
                let nextMonthNumber = nextMonth.getMonth() + 1;
                let nextDay = nextMonth.getDate();

                fechaProximoPago = new Date(nextYear, nextMonthNumber - 1, nextDay).toISOString().split('T')[0];
            }

            fetch('../instruments/actualizar_cuota_Socio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    socioUsuario: socioUsuario,
                    cuotaPagada: cuotaPagada,
                    fechaUltimoPago: isManualChange && cuotaPagada ? fechaUltimoPago : null,
                    fechaProximoPago: isManualChange && cuotaPagada ? fechaProximoPago : null
                }).toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (cuotaPagada) {
                        row.querySelector('.fecha-ultimo-pago').textContent = formatDate(fechaUltimoPago);
                        row.querySelector('.fecha-proximo-pago').textContent = formatDate(fechaProximoPago);
                    }
                } else {
                    alert('Error al actualizar la cuota');
                }
            });
        }

        function formatDate(dateString) {
            let [year, month, day] = dateString.split('-');
            return `${day}-${month}-${year}`;
        }

        document.getElementById('filter').addEventListener('change', function () {
            let filter = this.value;
            window.location.href = `?filter=${filter}`;
        });
    </script>
</body>

</html>
