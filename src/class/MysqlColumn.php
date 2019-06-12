<?php
class MysqlColumn{
    private $_header;
    private $_serverName;
    private $_tableName;
    private $_name;
    private $_datatype;
    private $_comment;
    private $_isPrimaryKey = false;
    private $_isMandatory = false;
    private $_default = false;

    private $_length = "20";
    private $_isSigned = false;

    function __construct(array $column)
    {
        $this->_serverName = $column['TABLE_SCHEMA'];
        $this->_tableName = $column['TABLE_NAME'];
        $this->_name = $column['COLUMN_NAME'];
        $this->_datatype = $column['DATA_TYPE'];
        $this->_comment = $column['COLUMN_COMMENT'];
        $this->_mandatory = ($column['IS_NULLABLE'] == "YES" ? true : false);
        $this->_default = $column['COLUMN_DEFAULT'];

        /** determine the column lengt */
        preg_match_all('!\d+\.*\d*!', $column['COLUMN_TYPE'], $matches);
        if (!empty($matches[0]))
        {
            $this->_length = $matches[0][0];
        } 
        /** is it a signed datatype? */
        $this->_isSigned = (strpos($column['COLUMN_TYPE'], "unsigend") > 0 ? true : false);
    }

    function getHeaderUrl(string $orderby = null, string $direction = null) : string
    {
        /** if the sorted column is the same as the current column we should set the next. Otherwise it is ascending. */
        if ($orderby == $this->_name)
        {
            $direction = ($direction == "ASC" ? $direction = "DESC" : $direction = "ASC" );
        } else {
            $direction = "ASC";
        }

        $title = $this->getLabel();

        return "<a href='controller.php?serverName=" . $this->_serverName . "&tableName=" . $this->_tableName . "&action=sort&orderby=" . $this->_name . "&direction=" . $direction . "'>" . $title . "</a>";
    }

    function getServer() : string
    {
        return $this->_serverName;
    }
    function getTable() : string
    {
        return $this->_tableName;
    }
    function getColumn() : string
    {
        return $this->_name;
    }
    function getDatatype() : string
    {
        return $this->_datatype;
    }
    function getLabel()
    {
        $title = $this->_name;
        if (!empty($this->_comment))
        {
            $title = $this->_comment;
        }
        return $title;        
    }

    function setPrimaryKey(bool $value)
    {
        $this->_isPrimaryKey = $value;
    }   
    function getPrimaryKey()
    {
        return $this->_isPrimaryKey;
    }
    function isMandatory()
    {
        return $this->_isMandatory;
    }
    function getDefault()
    {
        return $this->_default;
    }
    function isSigned()
    {
        return $this->_isSigned;
    }
    function getLength()
    {
        return $this->_length;
    }
}
?>