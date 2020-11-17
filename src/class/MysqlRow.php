<?php
require_once "MysqlDatabase.php";
require_once "MysqlField.php";
require_once "MysqlServer.php";

class MysqlRow{
    private $_db;
    private $_id;
    private $_serverName;
    private $_tableName;

    private $_row = [];
    private $_properties = [];

    function __construct(MysqlDatabase $db, string $serverName, string $tableName, int $id, array $properties, array $row)
    {
        $this->_db = $db;

        $this->_id = $id;
        $this->_serverName = $serverName;
        $this->_tableName = $tableName;

        $this->_properties = $properties;
        $this->_row = $row;
    }

    function createHtmlRow() : string 
    {
        $params['serverName'] = $this->_serverName;
        $params['tableName'] = $this->_tableName;
        $params['id'] =  $this->_id;
		$queryString =  http_build_query($params);

        $html = "<tr>";            
        $html .= "<td><input type='checkbox' name='' value='0'</td>";

        $keys = array_keys($this->_row);
        foreach($keys as $key)
        {
            $mysqlField = new MysqlField($this->_db, $this->_properties[$key], $this->_row[$key]);
            $html .=$mysqlField->getTableCell();
        }
        $html .= "<td>\n";
        $html .= "  <a class='btn btn-secondary' href='controller.php?action=edit&". $queryString . "' role='button'><i class='fas fa-edit'></i></a>\n";
        $html .= "  <a class='btn btn-danger' href='controller.php?action=delete&". $queryString . "' role='button'><i class='fas fa-trash-alt'></i></a>\n";
        $html .= "</td>\n";

        $html .= "</tr>";
        return $html;
    }
}
?>