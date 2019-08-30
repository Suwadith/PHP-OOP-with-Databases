<?php

class Bicycle {

  // ------ START OF ACTIVE RECODE CODE ------------ 

  protected static $database;

  protected static $db_columns = ['id', 'brand', 'model', 'year', 'category', 'color', 'description', 'gender', 'price', 'weight_kg', 'condition_id'];  

  public static function set_database($database) {
    self::$database = $database;
  }

  public static function find_by_sql($sql) {
    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }

    // results into objects
    
    $object_array = [];
    while($record = $result->fetch_assoc()) {
      $object_array[] = self::instantiate($record);
      // print_r($object_array);
    }

    $result->free();
    // print_r($object_array);
    return $object_array;
  }

  public static function find_all() {
    $sql = "SELECT * FROM bicycle";
    return self::find_by_sql($sql);
  }

  public static function find_by_id($id) {
    $sql = "SELECT * FROM bicycle ";
    $sql .= "WHERE id='" . self::$database->escape_string($id) . "'";
    $obj_array = self::find_by_sql($sql);
    if(!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

  protected static function instantiate($record) {
    $object = new self;
    // Could also manually assign values to properties
    // But automatic assignment is easier and re-usable
    foreach($record as $property => $value) {
      if(property_exists($object, $property)) {
        $object->$property = $value; // $property is a dynamic variable which will change according to the properties of the Bicycle objects (brand, model, etc.)
      }
    }
    // print_r($object);
    return $object;
    
  }

  public function create() {
    $attributes = $this->attributes();
    $sql = "INSERT INTO bicycle ";
    $sql .= "(";
    // $sql .= "brand, model, year, category, color, description, gender, price, weight_kg, condition_id";
    $sql .= join(', ', array_keys($attributes));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($attributes));
    // $sql .= "'" . $this->brand . "', ";
    // $sql .= "'" . $this->model . "', ";
    // $sql .= "'" . $this->year . "', ";
    // $sql .= "'" . $this->category . "', ";
    // $sql .= "'" . $this->color . "', ";
    // $sql .= "'" . $this->description . "', ";
    // $sql .= "'" . $this->gender . "', ";
    // $sql .= "'" . $this->price . "', ";
    // $sql .= "'" . $this->weight_kg . "', ";
    // $sql .= "'" . $this->condition_id . "'";
    $sql .= "')";
    $result = self::$database->query($sql);
    if($result) {
      $this->id = self::$database->insert_id;
    }
    return $result;
  }

  public function attributes() {
    $attributes = [];
    foreach(self::$db_columns as $column) {
      if($column == 'id') {
        continue;
      }
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }

  // ------ end OF ACTIVE RECODE CODE ------------ 

  public $id;
  public $brand;
  public $model;
  public $year;
  public $category;
  public $color;
  public $description;
  public $gender;
  public $price;
  protected $weight_kg;
  protected $condition_id;

  public const CATEGORIES = ['Road', 'Mountain', 'Hybrid', 'Cruiser', 'City', 'BMX'];

  public const GENDERS = ['Mens', 'Womens', 'Unisex'];

  public const CONDITION_OPTIONS = [
    1 => 'Beat up',
    2 => 'Decent',
    3 => 'Good',
    4 => 'Great',
    5 => 'Like New'
  ];

  public function __construct($args=[]) {
    //$this->brand = isset($args['brand']) ? $args['brand'] : '';
    $this->brand = $args['brand'] ?? '';
    $this->model = $args['model'] ?? '';
    $this->year = $args['year'] ?? '';
    $this->category = $args['category'] ?? '';
    $this->color = $args['color'] ?? '';
    $this->description = $args['description'] ?? '';
    $this->gender = $args['gender'] ?? '';
    $this->price = $args['price'] ?? 0;
    $this->weight_kg = $args['weight_kg'] ?? 0.0;
    $this->condition_id = $args['condition_id'] ?? 3;

    // Caution: allows private/protected properties to be set
    // foreach($args as $k => $v) {
    //   if(property_exists($this, $k)) {
    //     $this->$k = $v;
    //   }
    // }
  }

  public function name() {
    return "{$this->brand} {$this->model} {$this->year}";
  }

  public function weight_kg() {
    return number_format($this->weight_kg, 2) . ' kg';
  }

  public function set_weight_kg($value) {
    $this->weight_kg = floatval($value);
  }

  public function weight_lbs() {
    $weight_lbs = floatval($this->weight_kg) * 2.2046226218;
    return number_format($weight_lbs, 2) . ' lbs';
  }

  public function set_weight_lbs($value) {
    $this->weight_kg = floatval($value) / 2.2046226218;
  }

  public function condition() {
    if($this->condition_id > 0) {
      return self::CONDITION_OPTIONS[$this->condition_id];
    } else {
      return "Unknown";
    }
  }

}

?>
