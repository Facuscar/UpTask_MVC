<div class="dashboard">
    <?php if(!isset($_SESSION)){
        session_start();
    } ?>

    <?php include_once __DIR__ . '/../templates/sidebard.php'; ?>

    <div class="principal">
        <?php include_once __DIR__ . '/../templates/barra.php' ?>

        <div class="contenido">
        <h2 class="nombre-pagina"><?php echo $titulo ?></h2> 