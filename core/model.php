<?php

/**
 * Modelo base del cual extenderán los demás modelos.
 */
class Model {

    static $table;

    protected $exists;

    protected $id;

    public function __construct($data, $exists = false)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->exists = $exists;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function is_exist()
    {
        return $this->exists;
    }

    /**
     * Comprueba si el modelo es válido. Este método se sobreescribe en todos
     * los modelos y se añade la validación manualmente.
     * 
     * @return boolean true si la validación es incorrecta. false en caso contrario.
     */
    public function is_valid()
    {
        return true;
    }

    /**
     * Guarda o actualiza el modelo en la base de datos.
     */
    public function save()
    {
        // Las consultas de guardado se ejecutan en el método heredado.
        if ($this->exists) {
            //
        } else {
            // Se recoge el id de la nueva fila insertada.
            $this->id = DB::lastInsertId();
            // Se indica que el objeto existe en la DB.
            $this->exists = true;
        }
    }


    // Métodos estáticos

    /**
     * Obtiene modelos según la información enviada.
     * 
     * @param array $where Los atributos de la condición where. El array debe ser de clave - valor.
     * @param int $take_from Desde qué fila va a devolver. Si no se da este valor, se devuelven todos.
     * @param int $take Cuántas va a recoger. Si no se da este valor, $take_from se toma como $take.
     * @return mixed El objeto o el array de objetos devueltos.
     */
    static function where($where = [], $take_from = null, $take = null)
    {
        // Uso de static:: -> http://www.php.net/manual/es/language.oop5.late-static-bindings.php
        // static:: llama al método o variable estática de la clase desde la que se llama.
        // Si se hace User::where(), static::$table será el valor que tiene en
        // la clase User.
        $sql = 'SELECT * FROM ' . static::$table;
        $attributes = [];
        $one = false;

        // Se construye la sentencia SQL a partir de $where.
        if (count($where) > 0) {
            $sql .= ' WHERE';

            foreach ($where as $key => $value) {
                $sql .= sprintf(' %s = ? AND', $key);
                $attributes[] = $value;
            }

            $sql = substr($sql, 0, -4);
        }

        // Se incluye LIMIT si se han pasado $take_from y $take.
        if (is_int($take_from)) {
            if (!is_int($take)) {
                if ($take_from == 1) {
                    $one = true;
                }
                $take = $take_from;
                $take_from = 0;
            }
            $sql .= sprintf(' LIMIT %s,%s', $take_from, $take);
        }
        $sql .= ';';

        // Se ejecuta la consulta
        $result = DB::query($sql, $attributes);

        // Se obtiene el nombre de la clase desde la que se llamó el método
        // para poder crear los objetos con la clase correcta.
        $classname = get_called_class();

        // Si no se obtienen resultados, se devuelve false.
        if (count($result) == 0) {
            return false;

        // Si se pide un resultado, se devuelve el objeto.
        } elseif ($one) {
            return new $classname($result[0], true);

        // Si se obtienen varios objetos, se devuelve un array de objetos.
        } else {
            $collection = [];
            foreach ($result as $val) {
                $collection[] = new $classname($val, true);
            }
            return $collection;
        }
    }

    static function all()
    {
        return self::where();
    }

    /**
     * Crea un nuevo modelo con los datos pasados y lo almacena en la base de datos.
     * 
     * @param array $data Datos a pasar al constructor del modelo.
     * @return mixed El modelo creado, o false si ha ocurrido algún error al validar.
     */
    static function create($data)
    {
        $classname = get_called_class();
        $model = new $classname($data);
        if ($model->is_valid()) {
            $model->save();
            return $model;
        } else {
            return false;
        }
    }

}