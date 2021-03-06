<?php
require_once "MysqlServer.php";

class Mysql{
    private $_db;
    private $_servers = [];

    function __construct(MysqlDatabase $db){
        $this->_db = $db;
    }

    function getServers(){
        if (empty($this->_servers))
        {
            $sql = "SELECT * FROM information_schema.schemata";
            $items = $this->_db->select($sql);
            foreach ($items as $item)
            {
                $server = new MysqlServer($this->_db, $item);
                array_push($this->_servers, $server);
            }
        }
        return $this->_servers;
    }

    function showServersPage()
    {
        $html = "<h1>servers</h1>\n";

        $servers = $this->getServers();
        foreach($servers as $server)
        {
            $html .= $server->getUrl();
        }
        return $html;
    }
}
?>