<div class="contenedor olvide">
 
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Ingresa tu email y te enviaremos instrucciones para recuperar tu cuenta</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/olvide" method="POST" class="formulario">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu email" name="email">
            </div>
    
            <input type="submit" class="boton" value="Enviar instrucciones">
        </form>
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
            <a href="/">Recordé mi contraseña</a>
        </div>
    </div>
</div>