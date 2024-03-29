<?php

class QueryBuilder {


	/**
	 * Contains the query String
	 */
	public $query = "";


	/**
	 * Stores values for selective queries
	 */
	public $data = array();


	/**
	 * Constructor
	 *
	 * @param String $initial
	 */
	public function __construct ($initial = null) {
		if ($initial) $this->query = $initial . " ";
	}
	
	
	/**
	 * Select
	 *
	 * @param String $cloumn1,... Cloumns to select
	 */
	public function select () {
		$this->query .= "SELECT ";
		if (func_num_args() == 0) return $this->append("*");
		elseif(func_num_args() == 1 && is_array(func_get_arg(0))) $columns = func_get_arg(0);
		else $columns = func_get_args();	

		foreach ($columns as &$col) {
		    $parts = explode(" AS ", $col);
		    $parts[0] = $this->wrap($parts[0]);
		    if (isset($parts[1])) $parts[1] = "`".$parts[1]."`";
		    $col = implode(" AS ", $parts);
		}

		return $this->append(implode(",", $columns));		
	}
	

	/**
	 * Creates a from statement
	 *
	 * @param String $table
	 */
	public function from ($table) {
		$this->query .= "FROM " . $this->wrap($table) . " ";
		return $this;
	}


	/**
	 * Creates a join
	 *
	 * @param String $table
	 * @param String $col1
	 * @param String $col2
	 * @param String Type
	 */
	public function join ($table, $col1, $col2, $type = "") {
		if ($type) $this->query .= strtoupper($type) . " ";
		$this->query .= 
		    "JOIN " . $this->wrap($table) . 
		    " ON " . $this->wrap($col1)  . "=" . $this->wrap($col2) . " ";
		
		return $this;
	}


	/**
	 * Where
	 *
	 * @param String $column
	 * @param String $operator
	 * @param String $value
	 */
	public function where ($column, $operator, $value, $key = "where") {
		$this->query .= strtoupper($key) . " " . $this->wrap($column) . $operator . "? ";
		$this->data[] = $value;
		return $this;
	}


	/**
	 * In
	 *
	 * @TODO: WHERE A=B AND C IN (..)
	 */
	public function where_in ($column, $arr) {
		$argNum = sizeof($arr);
		if ($argNum == 0) return $this;
		if (strpos($this->query, "WHERE") === false) $this->query .= "WHERE ";

		$this->query .= $this->wrap($column) . " IN (" . rtrim(str_repeat("?,", $argNum),",") . ") ";
		$this->data = array_merge($this->data, $arr);

		return $this;
	}


	/**
	 * Appends a String to the query
	 *
	 * @param String $str
	 * @return this
	 */
	public function append ($str) {
		$this->query .= $str . " ";
		return $this;
	}


	/**
	 * Wraps a table name
	 *
	 * @param String $table
	 * @return String Tablename
	 */
	public function wrap ($table) {
		$keys = explode(".", $table);
		array_walk($keys, function (&$k) { $k = "`" . $k . "`";} );
		return  implode(".", $keys);
	}


	/**
	 * Returns the query
	 *
	 * @return String Query
	 */
	public function __toString () {
		return $this->query;
	}


	/** 
	 * Add a command to the query
	 *
	 * @param String $name
	 * @param Array $args
	 * @return this
	 */
	public function __call ($name, $args) {
		$func = explode("_", $name, 2);
		if (count($func) === 2) $args[] = $func[1];
		if (method_exists($this, $func[0])) return call_user_func_array(array($this, $func[0]), $args);

		$this->query .= strtoupper($name) . "()";
		$this->query .= " ";
		return $this;
	}


	/**
	 * Inserts the uppercased name
	 *
	 * @param String $name
	 * @return this
	 */
	public function __get ($name) {
		$this->query .= strtoupper($name) . " ";
		return $this;
	}
}