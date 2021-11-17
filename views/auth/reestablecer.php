<div class="contenedor reestablecer">
 
<?php 
include_once __DIR__ . '/../templates/nombre-sitio.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

    <div class="contenedor-sm">

    <?php if($mostrar){ ?>

        <p class="descripcion-pagina">Ingresa tu nueva contraseña</p>

        <form method="POST" class="formulario">
            <div class="campo">
                <label for="password">Nueva contraseña</label>
                <input type="password" id="password" placeholder="Tu contraseña" name="password">
            </div>
    
            <input type="submit" class="boton" value="Reestablecer contraseña">
        </form>

        <?php } ?>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
            <a href="/">Recordé mi contraseña</a>
        </div>
    </div>
</div>