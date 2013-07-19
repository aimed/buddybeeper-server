<?php

class DB {


	/**
	 * PDO Object
	 */
	public static $driver;


	/**
	 * Last query
	 */
	public static $_queries = array();
    

	/**
	 * Last statement
	 */
	private static $lastStatement = null;


	/**
	 * PDO Factory
	 *
	 * @param String $ns Driver Namespace
	 * @param String $username
	 * @param String $password
	 * @param Array $options
	 */
	public static function connect ($ns, $username = "", $password = "", $options = null) {
		self::$driver = new PDO($ns, $username, $password, $options);
	}


	/**
	 * Executes a query
	 * 
	 * @param String $query
	 * @param Array $data
	 * @return PDOStatement
	 */
	public static function query ($query, $data = null) {
		if ($data === null) 
		{
			$statement = self::$driver->query($query);
		} 
		else 
		{
			$statement = self::$driver->prepare($query);
			$statement->execute((array)$data);			
		}
		self::$_queries[] = $query;
		self::$lastStatement = $statement;
		return $statement;
	}
	

	/**
	 * Fetches a single row
	 *
	 * @param String $query
	 * @param Array $data
	 * @return Array Data
	 */
	public static function row ($query, $data = null, $type = PDO::FETCH_ASSOC) {
		$statement = self::query($query, $data);
		return $statement ? $statement->fetch($type) : null;
	}
	

	/**
	 * Fetches all results
	 * 
	 * @param String $query
	 * @param Array $data
	 * @return Array Data
	 */
	public static function fetch ($query, $data = null, $type = PDO::FETCH_ASSOC) {
		$statement = self::query($query, $data);
		return ($statement) ? $statement->fetchAll($type) : null;
	}


	/** 
	 * Inserts a set of data
	 *
	 * @param String $table
	 * @param Array $data
	 * @return Int Insert ID or NULL
	 */
	public static function insert ($table, $data = null, $ignore = false) {
		$query  = "INSERT ";
		if ($ignore) 
		{
		    $query .= "IGNORE "; 
		}
		$query .= "INTO `" . $table . "` ";
		$query .= "(";
		if (!empty($data)) $query .= "`" . implode("`,`", array_keys($data)) . "`";
		$query .= ") ";
		$query .= "VALUES ";
		$query .= "(" . rtrim(str_repeat("?,", count($data)),",") . ")";
		self::query($query, array_values($data));
		return (self::affectedRows() > 0) ? self::insertId() : null;
	}
	

	/**
	 * Deletes a set of data
	 *
	 * @param String $table
	 * @param Array $condition
	 * @return Integer Affected Rows
	 */
	public static function delete ($table, $condition) {
	    $query  = "DELETE FROM `" . $table . "` WHERE ";
	    $query .= self::serialize($condition, " AND ");
    
	    self::query($query);
	    return self::affectedRows();
	}
	

	/**
	 * Prepares a select query
	 *
	 * @param String $table
	 * @param Array $attrs
	 * @param Array $where
	 * @return String Query
	 */
	public static function prepareSelect ($table, $attrs, $where = null) {

	    if ($attrs && !is_array($attrs)) {
	        $attrs = array($attrs);
	    }
    
	    $query  = " SELECT " . implode(", ", $attrs);
	    $query .= " FROM " . self::table($table);
    
	    if($where)
	    {
	        $query .= " WHERE " . self::serialize($where, " AND ");
	    }
    
	    return $query;
	}	
	

	/**
	 * Grabs multiple sets of data
	 *
	 * @param String $table
	 * @param Array $attrs
	 * @param Array $where
	 */
	public static function grab ($table, $attrs, $where = null) {
	    return self::fetch(self::prepareSelect($table, $attrs, $where));
	}
	

	/**
	 * Grabs a set of data
	 *
	 * @param String $table
	 * @param Array $attrs
	 * @param Array $where
	 */
	public static function grabOne ($table, $attrs, $where = null) {
	    return self::row(self::prepareSelect($table, $attrs, $where));
	}
	
	
	/**
	 * Updates a set of data
	 *
	 * @param String $table
	 * @param Array $data
	 * @param Array $condition Optional
	 * @return Integer Affected Rows
	 */
	public static function update ($table, $data, $condition = false) {
	    $query = "UPDATE `".$table."` SET " . self::serialize($data);
    
	    if ($condition) 
	    {
	        $query .= " WHERE " . self::serialize($condition, " AND ");
	    }
    
	    self::query($query);
    
	    return DB::affectedRows();
	}


	/**
	 * Returns the number of rows affected by the last query
	 * 
	 * @return Integer Affected Rows
	 */
	public static function affectedRows () {
	    return empty(self::$lastStatement) ? null : self::$lastStatement->rowCount();
	}


	/**
	 * Gets the last inserted id
	 *
	 * @return Integer ID
	 */
	public static function insertId () {
		return self::$driver->lastInsertId();
	}


	/**
	 * Counts rows for the query
	 * 
	 * @return Integer Number of rows
	 */
	public static function count ($query, $data = null) {
	    return count(self::fetch($query, $data));
	}
	

	/**
	 * Escapes a table/cloumn name
	 *
	 * @param String $table
	 * @return String Escaped name
	 */
	public static function table ($table) {
	    $keys = explode(".", $table);
	    array_walk($keys, function (&$k) { $k = "`" . $k . "`";} );
	    return  implode(".", $keys);
	}


	/**
	 * Serializes and escapes a pair auf keys and values
	 *
	 * @param Array $array
	 * @param String $glue Optional
	 * @return String Serialized array
	 */
	public static function serialize ($array, $glue = ",") {
	    foreach ($array as $key => $value) 
	    {
	        $pairs[] = self::table($key) . "=" . self::$driver->quote($value);
	    }
    
	    return implode($glue, $pairs);
	}
}