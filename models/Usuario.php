<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //Validación para cuentas nuevas
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre de usuario es obligatorio';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña no puede ir vacia';
        }

        if(strlen($this->password) < 8){
            self::$alertas['error'][] = 'La contraseña debe tener al menos 8 caracteres';
        }

        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Las contraseñas no coinciden';
        }

        return self::$alertas;
    }         

    public function nuevoPassword($passwordActual, $passwordNuevo) : array {
        if(!$passwordActual){
            self::$alertas['error'][] = 'La contraseña actual no puede ir vacia';
        }
        if(!$passwordNuevo){
            self::$alertas['error'][] = 'La contraseña nueva no puede ir vacia';
        }

        if(strlen($passwordNuevo) < 8){
            self::$alertas['error'][] = 'La contraseña nueva debe contener al menos 8 caracteres';
        }

        return self::$alertas;
    }

    public function comprobarPassword($passwordActual) : bool {
        return password_verify($passwordActual, $this->password);
    }

    //Hashea el password del usuario
    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() : void{
        $this->token = uniqid();
    }

    //Valida el email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El campo es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El formato del email es inválido';
        }
        return self::$alertas;
    }

    public function validarNuevoPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña no puede ir vacia';
        }

        if(strlen($this->password) < 8){
            self::$alertas['error'][] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        return self::$alertas;
    }

    //Valida el login del usuario
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña no puede ir vacia';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El formato del email es inválido';
        }

        return self::$alertas;
    }

    public function validarPerfil(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre no puede ir vacio';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El correo no puede ir vacio';
        }

        return self::$alertas;
    }
}