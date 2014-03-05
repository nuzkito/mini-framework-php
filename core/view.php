<?php

/**
 * Maneja las vistas y devuelve el HTML con los datos insertados.
 * Las plantillas pueden extenderse de forma similar a las clases,
 * agregando una etiqueta que lo indique en el archivo de la plantilla.
 * 
 * Las variables, estructuras de control, etc. se usan con PHP normal,
 * incluyendo las etiquetas de apertura o cierre de PHP.
 */
class View {

    static $dir = 'views/';
    static $ext = '.php';
    static $locals = [];

    private $view;
    private $attrs;

    /**
     * Constructor de la clase. Recibe y guarda el nombre de la plantilla HTML a utilizar.
     * 
     * @param String $view Nombre de la pantilla HTML.
     */
    public function __construct($view)
    {
        $this->view = $view;
        $this->attrs = [];
    }

    /**
     * Extiende la vista
     * 
     * Recibe el HTML de la plantilla, comprueba si la plantilla extiende de
     * otra y, en ese caso, une las plantillas y devuelve el HTML correspondiente.
     * 
     * @param String $html HTML de la vista seleccionada.
     * @return String HTML completo, con el HTML de la vista de la que extiende(si extiende).
     */
    private function extend_view($html)
    {
        // Patrón que busca la etiqueta que indica si una plantilla extiende de otra.
        // Ejemplo: @extends 'nombre_plantilla' o @extends('nombre_plantilla')
        $pattern = '/@extends\s*\(?\s*[\'"]{1}(?<view>[A-Za-z0-9-_\/]+)[\'"]{1}\s*\)?/m';

        if (preg_match($pattern, $html, $matches)) {
            // Elimina el código de la etiqueta para que no se muestre en el HTML final.
            $html = preg_replace($pattern, '', $html);

            // Crea un nuevo objeto con la vista a la que extiende.
            $parent_view = View::make($matches['view']);
            // Le pasa a la nueva vista el HTML de la vista actual y las une.
            $parent_view->with('child', $html);
            // Devuelve el HTML final.
            $html = $parent_view->get();
        }

        return $html;
    }

    /**
     * Inserta variables en la vista.
     * 
     * Permite agregar una variable o un array de variables a la vista.
     * 
     * @param mixed $key Puede recibir un array con uno o varios elementos, o
     * el nombre de la variable de uno.
     * @param mixed $value Si $key no es un array, el valor correspondiente a
     * la variable $key.
     */
    public function with($key, $value)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->attrs[$k] = $v;
            }
        } else {
            $this->attrs[$key] = $value;
        }
    }

    /**
     * Devuelve el HTML procesado.
     * 
     * @return String El HTML procesado
     */
    public function get()
    {
        // Extrae las variables locales y los atributos para ser accedidos de
        // sencilla desde las plantillas.
        extract(self::$locals);
        extract($this->attrs);

        // Cierra el buffer de salida para evitar que se muestre en pantalla el HTML
        // antes de tiempo (el include de un archivo HTML mostraría ese HTML si no se
        // hiciera esto).
        ob_start();
        include self::$dir . $this->view . self::$ext;
        // Se guardan en una variable los datos del buffer...
        $html = ob_get_contents();
        // ... y se eliminan del mismo.
        ob_end_clean();

        // Se comprueba si la vista es extendible, y se extiende si es así.
        $html = $this->extend_view($html);

        return $html;
    }

    /**
     * Imprime la vista y finaliza la ejecución del programa.
     */
    public function draw()
    {
        die($this->get());
    }

    /**
     * Métodos estáticos.
     */

    /**
     * Crea un nuevo objeto de tipo View (identico a usar new View();).
     * 
     * @param String $view Nombre de la pantilla HTML.
     * @return View Nueva vista.
     */
    static function make($view)
    {
        return new self($view);
    }

    /**
     * Agrega variables locales que se podrán usar en todas las vistas.
     * 
     * @param mixed $key Puede recibir un array con uno o varios elementos, o
     * el nombre de la variable de uno.
     * @param mixed $value Si $key no es un array, el valor correspondiente a
     * la variable $key.
     */
    static function add_locals($key, $value)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                self::$locals[$k] = $v;
            }
        } else {
            self::$locals[$key] = $value;
        }
    }

    /**
     * Establece el directorio en el que se encuentran las plantillas.
     * 
     * @param String $dir Ruta del directorio.
     */
    static function set_dir($dir)
    {
        self::$dir = $dir;
    }

    /**
     * Establece la extensión de las plantillas (por defecto .php).
     * 
     * @param String $ext Extensión de las plantillas.
     */
    static function set_ext($ext)
    {
        self::$ext = $ext;
    }

}