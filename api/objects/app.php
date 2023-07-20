<?php


class App {

	private $conn;
    private $_query = null;
    private $_error = false;
    private $_results = [];
    private $_count = 0;
    protected $data = [];


	public function __construct($db){
		$this->conn = $db;
	}

	public function get_one($table, $where){
        $data = $this->select($table, $where);
        if ($data->count()) {
            $this->data = $data->first();
        }


        if(!$this->error()){
            $result = $this->data();
            if(!empty($result)){
                return $result;
            }
        }
        return false;

	}


    public function data() {
        return($this->data);
    }


    public function exists() {
        return(!empty($this->data));
    }



    public function action($action, $table, array $where = []) {
        if (count($where) === 3) {
            $operator = $where[1];
            $operators = ["=", ">", "<", ">=", "<="];
            if (in_array($operator, $operators)) {
                $field = $where[0];
                $value = $where[2];
                $params = [":value" => $value];
                if (!$this->query("{$action} FROM `{$table}` WHERE `{$field}` {$operator} :value", $params)->error()) {
                    return $this;
                }
            }
        } else {
            if (!$this->query("{$action} FROM `{$table}`")->error()) {
                return $this;
            }
        }
        return false;
    }

    public function query($sql, array $params = []) {
        $this->_count = 0;
        $this->_error = false;
        $this->_results = [];
        if (($this->_query = $this->conn->prepare($sql))) {
            foreach ($params as $key => $value) {
                $this->_query->bindValue($key, $value);
            }
            if ($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
                //die(print_r($this->_query->errorInfo()));
            }
        }
        return $this;
    }


    public function count() {
        return($this->_count);
    }

    public function delete($table, array $where = []) {
        return($this->action('DELETE', $table, $where));
    }

    public function error() {
        return($this->_error);
    }

    public function first() {
        return($this->results(0));
    }

    public function insert($table, array $fields) {
        if (count($fields)) {
            $params = [];
            foreach ($fields as $key => $value) {
                $params[":{$key}"] = $value;
            }
            $columns = implode("`, `", array_keys($fields));
            $values = implode(", ", array_keys($params));
            if (!$this->query("INSERT INTO `{$table}` (`{$columns}`) VALUES({$values})", $params)->error()) {
                return($this->conn->lastInsertId());
            }
        }
        return false;
    }

    public function results($key = null) {
        return(isset($key) ? $this->_results[$key] : $this->_results);
    }

    public function select($table, array $where = []) {
        return($this->action('SELECT *', $table, $where));
    }

    public function update($table, $where, array $fields) {
        if (count($fields)) {
            $x = 1;
            $set = "";
            $params = [];

            $where_field = $where[0];
            $where_value = $where[2];
            $params[":where_value"] = $where_value;
            foreach ($fields as $key => $value) {
                $params[":{$key}"] = $value;
                $set .= "`{$key}` = :$key";
                if ($x < count($fields)) {
                    $set .= ", ";
                }
                $x ++;
            }
            if (!$this->query("UPDATE `{$table}` SET {$set} WHERE  `{$where_field}` = :where_value", $params)->error()) {
                return true;
            }
        }
        return false;
    }


    public function get_countries(){

        $query = "SELECT id
                FROM countries";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        return $result;
    }

    public function get_last_id($table){
        $stmt = $this->conn->prepare("SELECT MAX(id) FROM $table");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_COLUMN, 0);
        if(!$result) return 0;
        return $result;
    }



}
