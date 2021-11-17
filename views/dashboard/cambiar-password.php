<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">

    <a href="/perfil" class="enlace">Volver al perfil</a>   
    
    <?php include __DIR__ . '/../templates/alertas.php' ?>

    <form class="formulario" method="POST" action="/cambiar-password">
        <div class="campo">
            <label for="password">Contraseña actual: </label>
            <input type="password" name="password" placeholder="Tu contraseña" >
        </div>

        <div class="campo">
            <label for="password2">Nueva contraseña: </label>
            <input type="password"  name="password2" placeholder="Nueva contraseña" >
        </div>

        <input type="submit" value="Guardar cambios">
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>