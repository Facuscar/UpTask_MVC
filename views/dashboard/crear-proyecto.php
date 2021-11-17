<?php include_once __DIR__ . '/header-dashboard.php'; ?>

    <div class="contenedor-sm">
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/crear-proyecto" method="POST" class="formulario">

            <?php  include_once __DIR__ . '/formulario-proyecto.php' ?>

            <input type="submit" value="Crear proyecto">
        </form>
    </div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>