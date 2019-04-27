<?php 
class MysqlColumn{
    var $_header;
    var $_server;
    var $_table;
    var $_name;
    var $_comment;

    function __construct(array $column)
    {
        $this->_server = $column['TABLE_SCHEMA'];
        $this->_table = $column['TABLE_NAME'];
        $this->_name = $column['COLUMN_NAME'];
        $this->_comment = $column['COLUMN_COMMENT'];

    }

    function getHeaderUrl(string $orderby, string $direction) : string 
    {
        /** if the sorted column is the same as the current column we should set the next. Otherwise it is ascending. */
        if ($orderby == $this->_name)
        {
            $direction = ($direction == "ASC" ? $direction = "DESC" : $direction = "ASC" );
        } else {
            $direction = "ASC";
        }

        $title = $this->_name;
        if (!empty($this->_comment))
        {
            $title = $this->_comment;
        }

        return "<a href='index.php?server=" . $this->_server . "&table=" . $this->_table . "&orderby=" . $this->_name . "&direction=" . $direction . "'>" . $title . "</a>";
    }

    function getColumn() : string 
    {
        return $this->_name;
    }
}
?>