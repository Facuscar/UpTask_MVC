<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){

        $alertas = [];

        if(!isset($_SESSION)){
            //Iniciar la sesión
            session_start();
        }

        if(isset($_SESSION['login'])){
            header('Location: /dashboard');
        }

        if(isset($_GET['exito'])){
            Usuario::setAlerta('exito','Contraseña reestablecida exitosamente');
        }
        

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            
            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                } else{
                    if( password_verify($_POST['password'], $usuario->password)){
                        $_SESSION['id']  = $usuario->id;
                        $_SESSION['nombre']  = $usuario->nombre;
                        $_SESSION['email']  = $usuario->email;
                        $_SESSION['login']  = true;

                        //Redireccionar
                        header('Location: /dashboard');
                    } else{
                        Usuario::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        //render a la vista
        $router->render('auth/login',[
            'titulo' => 'Iniciar sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        if(!isset($_SESSION)){
            session_start();
        }

        $_SESSION = [];

        header('Location: /');
    }

    public static function crear(Router $router){

        if(!isset($_SESSION)){
            //Iniciar la sesión
            session_start();
        }

        if(isset($_SESSION['login'])){
            header('Location: /dashboard');
        }

        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            $existeUsuario = Usuario::where('email', $usuario->email);

            if(empty($alertas)){
                if($existeUsuario){
                    Usuario::setAlerta('error', 'El correo ya se encuentra en uso');
                    $alertas = Usuario::getAlertas();
                } else{
                    //Hashear el password
                    $usuario->hashPassword();

                    //Eliminar password2
                    unset($usuario->password2);

                    //Generar el token
                    $usuario->crearToken();

                    //Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->email,$usuario->nombre,  $usuario->token);

                    $email->enviarConfirmacion();

                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }      
        }

        //Render a la vista
        $router->render('auth/crear',[
            'titulo' => 'Crea tu cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){

        if(!isset($_SESSION)){
            //Iniciar la sesión
            session_start();
        }

        if(isset($_SESSION['login'])){
            header('Location: /dashboard');
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email',$usuario->email);
                if(!$usuario){
                    Usuario::setAlerta('error', 'El usuario no existe');
                } else{
                    //Encontramos el usuario, por eso generamos un nuevo token
                    $usuario->crearToken();

                    //Actualizar el usuario
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    //Imprimir alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/olvide',[
            'titulo' => 'Recupera tu contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){

        if(!isset($_SESSION)){
            //Iniciar la sesión
            session_start();
        }

        if(isset($_SESSION['login'])){
            header('Location: /dashboard');
        }

        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token){
            header('Location: /');
        }

        //Identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'El token no es válido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //Validar nuevo password
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevoPassword();

            //Añadir el nuevo password
            if(empty($alertas)){
                //Hasheamos la nueva contraseña
                $usuario->hashPassword();

                //Eliminamos el token
                $usuario->token = null;

                //Guardamos los cambios en la base de datos
                $resultado = $usuario->guardar();

                if($resultado){
                    header('Location: /?exito=1');
                }
            }
            
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer',[
            'titulo' => 'Reestablece tu contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje',[
            'titulo' => 'Confirma tu cuenta'
        ]);
    }

    public static function confirmar(Router $router){

        if(!isset($_SESSION)){
            //Iniciar la sesión
            session_start();
        }

        if(isset($_SESSION['login'])){
            header('Location: /dashboard');
        }

        $alertas = [];

        if(!isset($_GET['token'])){
            Usuario::setAlerta('error', 'Token no encontrado');
        } else{
            $token = s($_GET['token']);

            $usuario = Usuario::where("token", $token);
    
            if(empty($usuario)){
                //Mostrar mensaje de error
                Usuario::setAlerta('error', 'Token no válido');
            } 
            else{
                //Modificar a usuario confirmado
                $usuario->confirmado = "1";
                $usuario->token = null;
    
                //Muestra el mensaje de exito
                Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
                $usuario->guardar();
            }
        }

        

        //Obtener las alertas
        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar',[
            'titulo' => 'Confirmando cuenta...',
            'alertas' => $alertas
        ]);

    }
}