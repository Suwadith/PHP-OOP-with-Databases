<?php

class DatabaseObject
{

    protected static $database;
    protected static $table_name = "";
    protected static $columns = [];
    public $errors = [];

    public static function set_database($database)
    {
        self::$database = $database;
    }

    public static function find_by_sql($sql)
    {
        $result = self::$database->query($sql);
        if (!$result) {
            exit("Database query failed.");
        }

        // results into objects

        $object_array = [];
        while ($record = $result->fetch_assoc()) {
            $object_array[] = static::instantiate($record);
            // print_r($object_array);
        }

        $result->free();
        // print_r($object_array);
        return $object_array;
    }

    public static function find_all()
    {
        $sql = "SELECT * FROM " . static::$table_name;
        return static::find_by_sql($sql);
    }

    public static function count_all()
    {
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        $result_set = self::$database->query($sql);
        $row = $result_set->fetch_array();
        return array_shift($row);
    }

    public static function find_by_id($id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE id='" . self::$database->escape_string($id) . "'";
        $obj_array = static::find_by_sql($sql);
        if (!empty($obj_array)) {
            return array_shift($obj_array);
        } else {
            return false;
        }
    }

    protected static function instantiate($record)
    {
        $object = new static;
        // Could also manually assign values to properties
        // But automatic assignment is easier and re-usable
        foreach ($record as $property => $value) {
            if (property_exists($object, $property)) {
                $object->$property = $value; // $property is a dynamic variable which will change according to the properties of the Bicycle objects (brand, model, etc.)
            }
        }
        // print_r($object);
        return $object;

    }

    protected function validate()
    {
        $this->errors = [];

        // add custom validation (Subclasses will override this method)

        return $this->errors;
    }

    protected function create()
    {

        $this->validate();
        if (!empty($this->errors)) {
            return false;
        }

        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . static::$table_name . " ";
        $sql .= "(";
        $sql .= join(', ', array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        $result = self::$database->query($sql);
        if ($result) {
            $this->id = self::$database->insert_id;
        }
        return $result;
    }

    protected function update()
    {

        $this->validate();
        if (!empty($this->errors)) {
            return false;
        }

        $attributes = $this->sanitized_attributes();
        $attribute_pairs = [];
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(', ', $attribute_pairs);
        $sql .= " WHERE id='" . self::$database->escape_string($this->id) . "' ";
        $sql .= "LIMIT 1";
        $result = self::$database->query($sql);
        return $result;
    }

    public function save()
    {
        // A new record will not have an ID yet
        if (isset($this->id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    public function merge_attributes($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) { // checks if the attribute is available & only updates if the form field is not null.
                $this->$key = $value;
            }
        }
    }

    public function attributes()
    {
        $attributes = [];
        foreach (static::$db_columns as $column) {
            if ($column == 'id') {
                continue;
            }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    protected function sanitized_attributes()
    {
        $sanitized = [];
        foreach ($this->attributes() as $key => $value) {
            $sanitized[$key] = self::$database->escape_string($value);
        }
        return $sanitized;
    }

    public function delete()
    {
        $sql = "DELETE FROM " . static::$table_name . " ";
        $sql .= "WHERE id='" . self::$database->escape_string($this->id) . "' ";
        $sql .= "LIMIT 1";
        $result = self::$database->query($sql);
        return $result;

        // After deleting the instance of the object will still exists,
        // even though the database record does not.
        // This can be useful, as in:
        // echo $user->name . "was deleted";
        // but we can't use $user->update() after
        // calling $user->delete();
    }

}
