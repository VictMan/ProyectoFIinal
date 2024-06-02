<header>
    <li>
        <?php if ($_SESSION['type'] === 'socio'): ?>
            <li><a href="user_view.php">Inicio</a></li>
        <?php else: ?>
            <li><a href="admin_view.php">Inicio</a></li>
        <?php endif; ?>
        <li><a href="configuracion.php">Configuración</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
    </nav>
</header>