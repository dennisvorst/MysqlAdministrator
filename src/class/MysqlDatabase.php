<?php
class MysqlDatabase{
	/**
	 * Mysql database connection class 
	 */

	 private $_connection;
	 private $_result;

	function __construct()
	{
		/** If there is a config file use that otherwise use the defaults */
		if ("config.php")
		{
			include "config.php";
		} else {
			/* localhost */
			if (!defined('NMSERVER')) define('NMSERVER', 'localhost');
			if (!defined('NMUSER'))define('NMUSER', 'root');
			if (!defined('NMPASSWORD'))define('NMPASSWORD', 'some_password');
			if (!defined('NMDATABASE'))define('NMDATABASE', 'some_database');
		}

			/* connect to the database */
		$this->_connection = mysqli_connect(NMSERVER, NMUSER, NMPASSWORD)
			or die("Kan geen verbinding maken met server \n" . mysqli_error($this->_connection));

		/* change character set to utf8 as read in: https://www.toptal.com/php/a-utf-8-primer-for-php-and-mysql */
		if (!mysqli_set_charset($this->_connection, "utf8")) {
			print_r("Error loading character set utf8: " . mysqli_error($this->_connection) . "\n");
		}

		// select the database
		$this->database  = mysqli_select_db($this->_connection, NMDATABASE)
			or die("Kan geen database selecteren\n");
	}

	function executeQuery($sql) : array 
	{
		/** execute the query and show the results */ 
		$this->_result = mysqli_query($this->_connection, $sql)
			or die("Error " . mysqli_errno($this->_connection) . ": "
			. mysqli_error($this->_connection) . " on execution of '" . $sql . "' query in executeQuery");

		// if only one record strip it.
		if (mysqli_affected_rows($this->_connection) == 1)
		{
			$this->_result = mysqli_fetch_array($this->_result, MYSQLI_ASSOC);
			$this->_result = array(0=>$this->_result);
		} else {
			/* put all the variables in an array */

			$output = [];
			while ($row = mysqli_fetch_array($this->_result, MYSQLI_ASSOC)) {
				array_push($output, $row);
			}
			$this->_result = $output;
		}
		return $this->_result;
	}

	function updateDatabase($sql) : int
	{
		/** perform an insert, delete or an update on the database */
		$result = mysqli_query($this->_connection, $sql)
		or die("Error " . mysqli_errno($this->_connection) . ": "
		. mysqli_error($this->_connection) . " on executing " . $sql);

		return mysqli_insert_id($this->_connection);
	}
}
