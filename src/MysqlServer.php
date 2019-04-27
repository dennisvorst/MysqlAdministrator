<?php
require_once "MysqlTable.php";

class MysqlServer{
    private $_name;
    private $_db;
    private $_tables = [];

    function __construct(Database $db, array $server)
    {
        $this->_db = $db;
        $this->_name = $server['SCHEMA_NAME'];
    }

    function getUrl() : string
    {
        return "<a href='index.php?server=" . $this->_name . "'>" . $this->_name . "</a><br />\n";
    }

    function showTablesPage() : string
    {
        $html = "<h1>Tables for " . $this->_name . "</h1>\n";

        $tables = $this->_getTables();
        foreach($tables as $table)
        {
            $html .= $table->getUrl();
        }

        return $html;
    }

    private function _getTables(){
        if (empty($this->_tables))
        {
            $sql = "SELECT * FROM information_schema.tables WHERE TABLE_SCHEMA = '" . $this->_name . "'";
            $items = $this->_db->queryDb($sql);
            foreach ($items as $item)
            {
                $table = new MysqlTable($this->_db, $item);
                array_push($this->_tables, $table);
            }
        } 
        return $this->_tables;
    }

}
?>