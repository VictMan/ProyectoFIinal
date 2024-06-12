<header>
    <div class="logo-container">
        <img src="../../public/img/LogoApp.jpg" id="logo">
        <span id='appName'>CashClubControl</span>
    </div>
    <div class="menu-icons">
        <?php if ($_SESSION['type'] === 'socio'): ?>
            <a href="user_view.php"><i class="fas fa-home"></i></a>
        <?php else: ?>
            <a href="admin_view.php"><i class="fas fa-home"></i></a>
        <?php endif; ?>
        <a href="configuracion.php"><i class="fas fa-cog"></i></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
    </div>
    <nav id="menu-hamburguesa">
        <input type="checkbox" id="hamburguesa">
        <label for="hamburguesa" class="fa fa-bars" id="icono"></label>
        <ul class="menu">
            <?php if ($_SESSION['type'] === 'socio'): ?>
                <li><a href="user_view.php">Inicio</a></li>
            <?php else: ?>
                <li><a href="admin_view.php">Inicio</a></li>
            <?php endif; ?>
            <li><a href="configuracion.php">Configuración</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
</header>
