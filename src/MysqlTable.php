<?php
require_once "MysqlColumn.php";

class MysqlTable{
    private $_db;

    private $_server;
    private $_name;

    private $_records;
    private $_columns = [];

    private $_orderby;
    private $_direction;

    function __construct(Database $db, array $table, string $orderby = null,string $direction = null)
    {
        $this->_db = $db;
        $this->_server = $table['TABLE_SCHEMA'];
        $this->_name = $table['TABLE_NAME'];

        $this->_orderby = $orderby;
        $this->_direction = $direction;        
    }

    function getUrl() : string 
    {
        return "<a href='index.php?server=" . $this->_server . "&table=" . $this->_name . "'>" . $this->_name . "</a><br />\n";
    }

    private function _getTableRows() : string
    {
        $sql = "SELECT * FROM " . $this->_server . "." . $this->_name;
        if (isset($this->_direction))
        {
            $sql .= " ORDER BY " . $this->_orderby . " " . $this->_getDirection();
        }

        $rows = $this->_db->queryDb($sql);

        $html = "";
        foreach ($rows as $row)
        {
            $html .= "<tr>";
            $html .= "<td><input type='checkbox' name='' value='0'</td>";

            foreach($row as $column)
            {
                $html .= "<td>" . $column . "</td>";
            }
            $html .= "<td>Delete</td><td>Edit</td>";

            $html .= "</tr>";
        }
        return $html;
    }

    function showRecordsPage()
    {
        ?>
        <h1>Records for <?php echo $this->_name ?></h1>
        <table>
            <tr>
                <?php echo $this->_getTableHeader();?>
            </tr>
            <tr>
                <?php echo $this->_getTableRows();?>
            </tr>
        </table>
        <?php
    }

    private function _getColumns()
    {
        if (empty($this->_columns)) 
        {
            $sql = "SELECT * FROM information_schema.columns WHERE TABLE_SCHEMA = '" . $this->_server . "' AND TABLE_NAME = '" . $this->_name . "'";
            $items = $this->_db->queryDb($sql);
            foreach ($items as $item)
            {
                $column = new MysqlColumn($item);
                array_push($this->_columns, $column);
            }
        }
        return $this->_columns;
    }

    private function _getTableHeader() : string
    {
        $html = "<th><input type='checkbox' name='' value='0'</th>";
        $columns = $this->_getColumns();
        foreach ($columns as $column)
        {
            $html .= "<th>" . $column->getHeaderUrl($this->_orderby, $this->_direction) . "</th>";
        }
        $html .= "<th>Delete</th><th>Edit</th>";

        return $html . "\n";
    }

    private function _getDirection()
    {
        if (empty($this->_direction)) {
            return "ASC";
        } 
        return $this->_direction;
    }
}
?>
