<?php

/**
 * Controlador base del cual extenderán los demás controladores. El controlador,
 * cuando se inicia, se le debe pasar el action, que es el nombre del método
 * que va a ejecutar.
 */
class Controller {

    static $require_auth = [];

    /**
     * El constructor recibe el action, que contiene el nombre del método que va a ejecutar.
     * Si el método no existe, muestra un error 404. Si existe, envía al cliente la vista
     * y finaliza la ejecución del programa.
     * 
     * @param String $action Nombre del método a ejecutar.
     */
    public function __construct($action = null)
    {
        // Comprueba si action es distinto de null, y en dicho caso, si equivale a un método
        // que exista en el controlador.
        if ($action !== null && method_exists($this, $action)) {
            // Comprueba si el método requiere estar autenticado, y lo
            // redirige al login si no lo está.
            if (in_array($action, static::$require_auth)) {
                if (!Auth::check()) {
                    die(header('Location:./'));
                }
            }

            // Ejecuta el método guardado en $action. Ejemplo: Si $action = 'login', hará $this->login()
            $view = $this->$action();
            // Si el contenido devuelto por el método es una instancia de la clase View,
            // ejecuta el método que imprime la vista.
            if ($view instanceof View) {
                $view->draw();
            // Si no, muestra el contenido devuelto.
            } else {
                echo $view;
            }
            die;
        }
    }

}