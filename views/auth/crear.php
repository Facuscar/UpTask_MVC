<div class="contenedor crear">
 
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu cuenta</p>

        <?php include_once __DIR__ . '/../templates/alertas.php';?>

        <form action="/crear" method="POST" class="formulario">

        <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" placeholder="Tu nombre" name="nombre" value="<?php $usuario->nombre ?? '';?>">
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu email" name="email" value="<?php $usuario->email ?? '';?>">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="Tu contraseña" name="password">
            </div>

            <div class="campo">
                <label for="password2">Repite tu contraseña</label>
                <input type="password" id="password2" placeholder="Repite la contraseña" name="password2">
            </div>

            <input type="submit" class="boton" value="Crear cuenta">
        </form>
        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar sesión</a>
            <a href="/olvide">¿Olvidaste tu contraseña? Recuperar cuenta</a>
        </div>
    </div>
</div>