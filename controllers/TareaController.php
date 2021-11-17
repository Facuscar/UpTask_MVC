<?php 

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController{

    public static function index(){
        if(!isset($_SESSION)){
            session_start();
        }

        $proyectoURL = $_GET['id'];

        if(!$proyectoURL) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoURL);

        //Verificamos que el proyecto exista y que corresponda al propietario
        if(!$proyecto || $_SESSION['id'] !== $proyecto->propietarioId) header('Location: /404');

        //Obtenemos las tareas del proyecto
        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas]);
    }

    public static function crear(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!isset($_SESSION)){
                session_start();
            }
            //Obtenemos el proyecto validando por la URL
            $proyecto = Proyecto::where('url', $_POST['proyectoURL']);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agregar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            } 

            //Todo bien, instanciar y crear la tarea
            $tarea = new Tarea($_POST);
            //Agregamos el id del proyecto a la tarea
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();

            $respuesta = [
                'tipo' => 'exito',
                'id' =>$resultado['id'],
                'mensaje'=>'Tarea creada correctamente',
                'proyectoId' => $proyecto->id
            ];

            echo json_encode($respuesta);
        }
    }

    public static function actualizar(){

        if(!isset($_SESSION)){
            session_start();
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //Validamos que el proyecto exista
            $proyecto = Proyecto::where('url', $_POST['proyectoURL']);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            } 

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;

            $resultado = $tarea->guardar();

            if($resultado){
                $respuesta = [
                    'tipo' => 'exito',
                    'id' =>$tarea->id,
                    'proyectoId' => $proyecto->id,
                    'mensaje' => 'Actualizado correctamente'
                ];
            }

            echo json_encode(['respuesta' =>  $respuesta]);
        }
    }

    public static function eliminar(){

        if(!isset($_SESSION)){
            session_start();
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $proyecto = Proyecto::where('url', $_POST['proyectoURL']);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            } 

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->eliminar();

            $resultado = [
                'resultado' => $resultado,
                'mensaje' => 'Eliminado correctamente',
                'tipo' => 'exito'
            ];

            echo json_encode($resultado);
        }
    }
}