<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashBoardController {
    public static function index(Router $router){

        if(!isset($_SESSION)){
            session_start();
        }

        isAuth();

        $proyectos = Proyecto::belongsTo('propietarioId', $_SESSION['id']);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear (Router $router){
        if(!isset($_SESSION)){
            session_start();
        }

        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);

            //Validación
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                //Generar el URL del proyecto
                $proyecto->url = md5(uniqid());

                //Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //Guardar el proyecto
                $proyecto->guardar();

                //Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        if(!isset($_SESSION)){
            session_start();
        }

        isAuth();

        $token = $_GET['id'];

        if(!$token) header('Location: /dashboard');

        //Revisar que la persona que visita el proyecto es el creador
        $proyecto = Proyecto::where('url', $token);

        if($proyecto->propietarioId != $_SESSION['id']){
            header('Location: /dashboard');
        }
        


        $alertas = [];

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto,
            'alertas' => $alertas
        ]);
    }

    public static function perfil (Router $router){
        if(!isset($_SESSION)){
            session_start();
        }

        isAuth();

        $usuario = Usuario::find($_SESSION['id']);

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarPerfil();

            if(empty($alertas)){
                //Verificar que el email no esté registrado en otra cuenta
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $usuario->id !== $existeUsuario->id){
                    //El email está ocupado, entonces muestro un mensaje de error
                    Usuario::setAlerta('error', 'El correo ya está en uso');
                } else{
                    //El email no está en uso
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guardado correctamente');
    
                    //Sincronizamos la variable $_SESSION con los cambios
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['email'] = $usuario->email;
                }

               
            }
        }

        $alertas = $usuario->getAlertas();

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router){
       
        if(!isset($_SESSION)){
            session_start();
        }

        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            
            $usuario = Usuario::find($_SESSION['id']);


            $alertas = $usuario->nuevoPassword($_POST['password'], $_POST['password2']);            
            
            if(empty($alertas)){
                $resultado = $usuario->comprobarPassword($_POST['password']);
                
                if($resultado) {
                    //Asignamos el nuevo password
                    $usuario->password = $_POST['password2'];

                    //Hasheamos el nuevo password
                    $usuario->hashPassword();

                    //Guardamos los cambios 
                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito', 'Contraseña actualizada correctamente.');
                        $alertas = $usuario->getAlertas();
                    }

                } else{
                    Usuario::setAlerta('error', 'Password incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar contraseña',
            'alertas' => $alertas
        ]);
    }
}