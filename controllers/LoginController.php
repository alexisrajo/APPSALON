<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Email;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            
            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                //COMPROBAR QUE EXISTE USUARIO
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    //VERIFICAR PASSWORD
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        //AUTENTICAR EL USUARIO
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //REDIRECCIONAMIENTO
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();

        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1") {

                    //GENERAR UN TOKEN
                    $usuario->crearToken();
                    $usuario->guardar();

                    //ENVIAR EL EMAIL
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarInstrucciones();


                    //ALERTA DE EXITO
                    Usuario::setAlerta('exito', 'Revisa tu email');
                    
                } else {
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                  
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {

        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        //BUSCAR USUARIO POR SU TOKEN
        $usuario = Usuario::where('token',$token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // LEER EL NUEVO PASSWORD Y GUARDARLO
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;  //elimina password anterior

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }
        }
  
        $alertas = Usuario::getAlertas();
        
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {

        $usuario = new Usuario;

        //ALERTAS VACIAS
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // REVISAR QUE ALERTA ESTE VACIO
            if(empty($alertas)) {
                //VERIFICAR QUE USUARIO NO ESTE REGISTRADO
                $resultado = $usuario -> existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //HASHEAR PASSWORD
                    $usuario->hashPassword();

                    //GENERAR UN TOKEN UNICO
                    $usuario->crearToken();

                    //ENVIAR EL EMAIL
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    //CREAR EL USUARIO  
                    $resultado = $usuario->guardar();
                    if($resultado){
                        header('Location: /mensaje');
                    }

                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {
        $alertas = [];

        $token = s($_GET['token']);
        
        $usuario = Usuario::where('token', $token);
        if(empty($usuario)) {
            //MOSTRAR MENSAJE DE ERROR
            Usuario::setAlerta('error', 'Token No Valido');
        } else {
            //MODIFICAR A USUARIO CONFIRMADO
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        //OBTENER ALERTAS
        $alertas = Usuario::getAlertas();

        //RENDERIZAR LA VISTA
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}