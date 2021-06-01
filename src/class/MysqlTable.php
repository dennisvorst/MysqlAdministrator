<?php
require_once "MysqlColumn.php";
require_once "MysqlRow.php";


class MysqlTable{
    private $_db;

    private $_serverName;
    private $_name;
    private $_pk;

    private $_records = [];
    private $_columns = [];

    private $_orderby;
    private $_direction;

    function __construct(MysqlDatabase $db, array $table, string $orderby = null,string $direction = null)
    {
        $this->_db = $db;
        $this->_serverName = $table['TABLE_SCHEMA'];
        $this->_name = $table['TABLE_NAME'];

        $this->_orderby = $orderby;
        $this->_direction = $direction;
    }

    function getUrl() : string
    {
        return "<a href='controller.php?". $this->_getQueryString() . "'>" . $this->_name . "</a><br />\n";
    }

    function insertRecord(array $values) : int
    {
        $columns = $this->_getColumns();
        $keys = array_keys($columns);
        $queryvalues = [];
        $types = "";
        $row = [];

        foreach ($keys as $key)
        {
            $value = (key_exists($key, $values) ? $values[$key] : null);
            $mysqlField = new MysqlField($this->_db, $columns[$key], $value);

            if (strlen($value) > 0)
            {
                $queryvalues[] = $mysqlField->getQueryPlaceholder();
                $types .= $mysqlField->getQueryDataType();
                $row[] = $mysqlField->getQueryValue();
            } else {
                $queryvalues[] = "NULL";
            }
        }

        $sql = "INSERT INTO " . $this->_getFullTableName() . "(" . implode(", ", $keys) . ") ";
        $sql .= "VALUES (" . implode(", ", $queryvalues) . ")";
        $id = $this->_db->insert($sql, $types, $row);
        return $id;
    }

    /** delete a record using the primary key */
    function deleteRecord($id)
    {
        $pk = $this->_getPrimaryKey();
		$sql = "DELETE FROM " . $this->_getFullTableName() . " WHERE {$pk} = ?";

        $id = $this->_db->delete($sql, "i", [$id]);
    }

    function updateRecord(array $values)
    {
        /** get the columns */
        $columns = $this->_getColumns();
        $types = "";
        $row = [];

        /** get the primary key, and take out the value */
        $pk = $this->_getPrimaryKey();
        $id = $values[$pk];
        unset($values[$pk]);

        /** get the original record */
        $originalRows = $this->_getRecordById($id);
        unset($originalRows[$pk]);
        $keys = array_keys($originalRows);

        /** get the keys */
        $columnList = "";
        foreach($keys as $key)
        {
            if ($originalRows[$key] !== $values[$key])
            {
                $mysqlField = new MysqlField($this->_db, $columns[$key], $values[$key]);
                if (!empty($columnList))
                {
                    $columnList .= ", ";
                }
                /** it the field has no value supply a NULL else add it to the columnlist */ 
                $value = $mysqlField->getQueryValue();
                if ($value  == "NULL")
                {
                    $columnList .= $key . " = " . $value;
                } else {
                    $columnList .= $key . " = ?";
                    $types .= $mysqlField->getQueryDataType();
                    $row[] = $value;
                }
            }
        }

        if (!empty($columnList))
        {
            $sql = "UPDATE " . $this->_getFullTableName() . " SET " . $columnList . " WHERE " . $pk . " = ?";
            $types .= "i";
            $row[] = $id;

            $id = $this->_db->update($sql, $types, $row);
        }
    }

    private function _showTableRows() : string
    {
        /** get the primary key */
        $pk = $this->_getPrimaryKey();

        /** get the properties */
        $properties = $this->_getColumns();

        /** get the records */
        $rows = $this->_getRecords();

        /** create the html */
        $html = "";
        foreach ($rows as $row)
        {
            $mysqlRow = new MysqlRow($this->_db, $this->_serverName, $this->_name, $row[$pk], $properties, $row);
            $html .= $mysqlRow->createHtmlRow();
        }
        return $html;
    }

    function showRecordsPage()
    {
        ?>
        <h1>Showing records for <?php echo $this->_name ?></h1>
        <?php
        $rows = $this->_getRecords();
        if (count($rows) > 0)
        {
        ?>
        <table>
            <tr>
                <?php echo $this->_showTableHeader();?>
            </tr>
            <tr>
                <?php echo $this->_showTableRows();?>
            </tr>
        </table>
        <?php
        } else {
            ?>
            <div>No records found for table <?php echo $this->_name; ?></div>
            <?php
        }
        ?>
        <!-- the button -->
        <div class="btn-group" role="group">
            <a class="btn btn-primary" href="controller.php?action=create&serverName=<?php echo $this->_serverName . "&tableName=" . $this->_name; ?>" role="button">New</a>
        </div>
        <?php
    }

    function showInsertPage()
    {
        /** get the primary key */
        $pk = $this->_getPrimaryKey();

        /** get the column definitions */
        $columns = $this->_getColumns();

        $keys = array_keys($columns);

        ?>
        <!-- html -->
        <h1>Creating a record in table <?php echo $this->_getFullTableName(); ?></h1>

        <form class="form-horizontal" action="controller.php" enctype="multipart/form-data" method="POST">
            <!-- visible fields -->
            <?php
            foreach ($keys as $key)
            {
                $mysqlField = new MysqlField($this->_db, $columns[$key]);
                $mysqlField->getEditableObject();
            }

            if (method_exists($this->_name, "addtionalFormProperties"))
            {
                $class = new $this->_name($this->_db);
                echo $class->addtionalFormProperties();

            } 
            ?>

            <!-- control fields -->
            <input type="hidden" id="action" name="action" value="insert">
            <input type="hidden" id="database" name="serverName" value="<?php echo (isset($this->_serverName) ? $this->_serverName : ""); ?>">
            <input type="hidden" id="table" name="tableName" value="<?php echo (isset($this->_name) ? $this->_name : ""); ?>">

            <!-- buttons -->
            <div class="btn-group" role="group">
                <button class="btn btn-secondary" type="submit">Ok</button>
                <button class="btn btn-secondary" type="submit">Cancel</button>
            </div>
        </form>
        <?php
    }

    function showEditPage(int $id, string $action)
    {
        /** get the properties */
        $properties = $this->_getColumns();

        /** get the record */
        $row = $this->_getRecordById($id);

        /** get the keys */
        $keys = array_keys($row);
        ?>

        <!-- html -->
        <h1>Updating record in table <?php echo $this->_getFullTableName(); ?></h1>
        <form class="form-horizontal" action="controller.php" enctype="multipart/form-data" method="POST">
            <!-- visible fields -->
            <?php
            foreach ($keys as $key)
            {
                $mysqlField = new MysqlField($this->_db, $properties[$key], $row[$key]);
                $mysqlField->getEditableObject();
            }
            ?>

            <!-- control fields -->
            <input type="hidden" id="action" name="action" value="update">
            <input type="hidden" id="database" name="serverName" value="<?php echo (isset($this->_serverName) ? $this->_serverName : ""); ?>">
            <input type="hidden" id="table" name="tableName" value="<?php echo (isset($this->_name) ? $this->_name : ""); ?>">

            <!-- buttons -->
            <div class="btn-group" role="group">
                <button class="btn btn-secondary" type="submit">Ok</button>
                <button class="btn btn-secondary" type="submit">Cancel</button>

                <?php
                if (isset($id))
                {
                ?>
                <button class="btn btn-danger" type="submit">Delete</button>
                <?php
                }
                ?>
            </div>

            <?php
            echo $this->showRelationalButtons($id);
            ?>

        </form>
        <?php
    }

    function showRelationalButtons(int $id)
    {
        /**
         * TABLE_SCHEMA and TABLE_NAME is the many
         * REFERENCE_TABLE_SCHEMA and REFERENCED_TABLE_NAME is the parent
         */
        if (!empty($id))
        {
            $sql = "SELECT * FROM information_schema.key_column_usage WHERE referenced_table_schema = ? AND referenced_table_name = ?";
            $rows = $this->_db->select($sql, "ss", [$this->_serverName, $this->_name]);
            ?>
            <div class="btn-group">
                <?php
                foreach ($rows as $row)
                {
                    $params['action'] = "create_child";
                    $params['parentServer'] = $this->_serverName;
                    $params['parentTable'] = $this->_name;
                    $params['serverName'] = $row['TABLE_SCHEMA'];
                    $params['tableName'] = $row['TABLE_NAME'];
                    $params['columnName'] = $row['COLUMN_NAME'];
                    $params['columnValue'] = $id;


                ?>
                <a class="btn btn-primary" href="controller.php?<?php echo http_build_query($params); ?>" role="button">Add <?php echo $row['TABLE_NAME']; ?></a>
                <?php

                }
                ?>
            </div>
            <?php
        }
    }

    private function _getColumns()
    {
        if (empty($this->_columns))
        {
            /** get the primary key */
            $pk = $this->_getPrimaryKey();

            /** get the columns */
            $sql = "SELECT * FROM information_schema.columns WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ";
            $items = $this->_db->select($sql, "ss", [$this->_serverName, $this->_name]);
            foreach ($items as $item)
            {
                $column = new MysqlColumn($item);
                if ($item['COLUMN_NAME'] == $pk)
                {
                    $column->setPrimaryKey(true);
                }
                $this->_columns[$item['COLUMN_NAME']] = $column;
            }
        }
        return $this->_columns;
    }

    private function _showTableHeader() : string
    {
        $html = "<th><input type='checkbox' name='' value='0'</th>";
        $columns = $this->_getColumns();
        foreach ($columns as $column)
        {
            $html .= "<th>" . $column->getHeaderUrl($this->_orderby, $this->_direction) . "</th>";
        }
        $html .= "<th colspan=2>Buttons</th>";

        return $html . "\n";
    }

    private function _getDirection()
    {
        if (empty($this->_direction)) {
            return "ASC";
        }
        return $this->_direction;
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

    private function _getQueryString() : string
    {
        return "serverName=" . $this->_serverName . "&tableName=" . $this->_name;
    }

    function getName()
	{
		return $this->_name;
    }

    private function _getPrimaryKey()
    {
        if (empty($this->_pk))
        {

            $sql = "SELECT column_name ";
            $sql .= "FROM information_schema.key_column_usage ";
            $sql .= "WHERE table_schema = ? ";
            $sql .= "AND table_name = ? ";
            $sql .= "AND constraint_name = 'PRIMARY'";

            $pk = $this->_db->select($sql, "ss", [$this->_serverName, $this->_name]);
            $pk = $pk[0]['column_name'];
        }
        return $pk;
    }

    private function _getRecords()
    {
        if(empty($this->_records))
        {
            $sql = "SELECT * FROM " . $this->_getFullTableName(); 

            if (!empty($this->_direction) && !empty($this->_orderby))
            {
                $sql .= " ORDER BY " . $this->_orderby . " " . $this->_getDirection();
            }
            $sql .= " LIMIT 0, 30";
            $this->_records = $this->_db->select($sql);
        }
        return $this->_records;
    }

    private function _getRecordById(int $id)
    {
        /** get the primary key of the table */
        $pk = $this->_getPrimaryKey();

        /** get the records */
        $sql = "SELECT * FROM " . $this->_getFullTableName() . " WHERE " . $pk . " = ? " ;
        $row = $this->_db->select($sql, "i", [$id]);

        return $row[0];
    }

    /** get hte fully qualified table name */
    private function _getFullTableName() : string
    {
        return $this->_serverName . "." . $this->_name;
    }

    /** get a list of key value pairs to use in a dropdown box */
    function getValueRepresentation() : array
    {
        $pk = $this->_getPrimaryKey();
        $valrep = $this->_getFirstStringField();

        $sql = "SELECT " . $pk . ", " . $valrep . " FROM " . $this->_getFullTableName() . " ORDER BY " . $valrep;
        $rows = $this->_db->select($sql);

        $values = [];
        foreach ($rows as $row)
        {
            $values[$row[$pk]] = $row[$valrep];

        }
        return $values;
    }

    private function _getFirstStringField()
    {
        $columns = $this->_getColumns();

        foreach ($this->_columns as $column)
        {
            switch($column->getDatatype())
            {
                case "char":
                case "varchar":
                    return $column->getColumn();
            }

        }
    }
}
?>
