<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\LoginController;
use Controllers\DashBoardController;
use Controllers\TareaController;
use MVC\Router;
$router = new Router();

//Login
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

//Crear cuenta
$router->get('/crear', [LoginController::class, 'crear']);
$router->post('/crear', [LoginController::class, 'crear']);

//Formulario de olvide mi contraseña
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);

//Colocar el nuevo password
$router->get('/reestablecer', [LoginController::class, 'reestablecer']);
$router->post('/reestablecer', [LoginController::class, 'reestablecer']);

//Confirmación de cuenta
$router->get('/mensaje', [LoginController::class, 'mensaje']);
$router->get('/confirmar', [LoginController::class, 'confirmar']);

//Zona de proyectos
$router->get('/dashboard', [DashBoardController::class, 'index']);
$router->get('/crear-proyecto', [DashBoardController::class, 'crear']);
$router->post('/crear-proyecto', [DashBoardController::class, 'crear']);
$router->get('/proyecto', [DashBoardController::class, 'proyecto']);
$router->get('/perfil', [DashBoardController::class, 'perfil']);
$router->post('/perfil', [DashBoardController::class, 'perfil']);
$router->get('/cambiar-password', [DashBoardController::class, 'cambiar_password']);
$router->post('/cambiar-password', [DashBoardController::class, 'cambiar_password']);

//API para las tareas
$router->get('/api/tareas', [TareaController::class, 'index']);
$router->post('/api/tarea', [TareaController::class, 'crear']);
$router->post('/api/tarea/actualizar', [TareaController::class, 'actualizar']);
$router->post('/api/tarea/eliminar', [TareaController::class, 'eliminar']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();