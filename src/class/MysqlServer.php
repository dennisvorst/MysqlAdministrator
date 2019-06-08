<?php
require_once "MysqlTable.php";

class MysqlServer{
    private $_name;
    private $_db;
    private $_tables = [];

    function __construct(MysqlDatabase $db, array $server)
    {
        $this->_db = $db;
        $this->_name = $server['SCHEMA_NAME'];
    }

    function getUrl() : string
    {
        return "<a href='controller.php?" . $this->_getQueryString() . "'>" . $this->_name . "</a><br />\n";
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

    function getTables(){
        if (empty($this->_tables))
        {
            $sql = "SELECT * FROM information_schema.tables WHERE TABLE_SCHEMA = '" . $this->_name . "'";
            $items = $this->_db->executeQuery($sql);
            foreach ($items as $item)
            {
                $table = new MysqlTable($this->_db, $item);
                array_push($this->_tables, $table);
            }
        }
        return $this->_tables;
    }

	function getName()
	{
		return $this->_name;
	}

    function getBreadCrumb(bool $isActive = false) : string
    {
        if ($isActive)
        {
            return "<li class='breadcrumb-item active' aria-current='page'>" . $this->getName() . "</li>\n";
        } else {
            return "<li class='breadcrumb-item'><a href='controller.php?" . $this->_getQueryString() . "'>" . $this->getName() . "</a></li>\n";
        }
    }
    function _getQueryString()
    {
        return "serverName=" . $this->_name;
    }
}
?>