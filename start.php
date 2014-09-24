<?php

define('PATH', dirname(__FILE__) . '/');

// Carga de todos los archivos necesarios.
require PATH . 'core/Config.php';
require PATH . 'core/Helpers.php';
require PATH . 'core/Database.php';
require PATH . 'core/Router.php';
require PATH . 'core/Route.php';
require PATH . 'core/Model.php';
require PATH . 'core/Controller.php';
require PATH . 'core/View.php';
require PATH . 'core/Auth.php';

if (isset($config['db']) && $config['db'] &&
    isset($config['db']['host']) && $config['db']['host'] &&
    isset($config['db']['port']) && $config['db']['port'] &&
    isset($config['db']['name']) && $config['db']['name'] &&
    isset($config['db']['user']) && $config['db']['user'])
{
	Database::init($config['db']);// Inicia una conexión con la base de datos.
	unset($config['db']); // Se eliminan los datos de la base de datos, por si acaso.
}

View::set_dir(PATH . 'views/'); // Define el directorio en el que se encuentran las vistas.
session_start(); // Inicia las sesiones


// Se obtiene el parámetro get de la url para cargar la página. Si no existe,
// se pone por defecto la página 'home'.
$page = (isset($_GET['page']) && $_GET['page'] != '') ? strtolower($_GET['page']) : 'home';

// Se carga el controlador asociado con la página. El resultado se guarda en
// una variable. Si el archivo no se encuentra quiere decir que la página
// no existe y devuelve false. En ese caso, se muestra un error 404.
// Si el archivo cargó correctamente, se ejecuta la clase.
$controller_file = PATH . 'controllers/' . $page . '.php';
if (is_file($controller_file)) {
    // Incluye el archivo.
    require $controller_file;
    // Guarda el nombre de la clase. Los controladores tienen una nomenclatura como la siguiente: NombreController.
    $class_name = ucfirst($page) . 'Controller';

    // Recoge el action(la acción a realizar en la página) de los parámetros
    // de la url. Si no hay un action definido, se llamará al método index.
    $action = (isset($_GET['action']) && $_GET['action'] != '') ? strtolower($_GET['action']) : 'index';
    // Crea el objeto.
    new $class_name($action);
}

error_404();
