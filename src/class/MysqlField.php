<?php

foreach (glob('class/museum/*.php') as $file)
{
    require_once $file;
}

//require '../vendor/autoload.php';

require_once "HtmlField.php";

class MysqlField
{
    private $_debug = true;

    private $_db;
    private $_log;

    private $_value;
    private $_dataType;

    private $_serverName;
    private $_tableName;
    private $_name;
    private $_isPrimaryKey = false;
    private $_isForeignKey = false;
    private $_isMandatory = false;
    private $_default;
    private $_label;
    private $_length;
    private $_isSigned = false;

	/* relationship information */
    private $_parentSchema;
    private $_parentTable;
    private $_parentDField;
    private $parentObject;

    function __construct(MysqlDatabase $db, MysqlColumn $column, $value = null)
    {
        $this->_db = $db;
        $this->_log = new Log("mysql.log");

        $this->_value = $value;
        $this->_dataType = $column->getDatatype();

        $this->_serverName = $column->getServer();
        $this->_tableName = $column->getTable();

        $this->_name = $column->getColumn();
        $this->_isPrimaryKey = $column->getPrimaryKey();
        $this->_isMandatory = $column->isMandatory();
        $this->_label = $column->getLabel();
        $this->_default = $column->getDefault();
        $this->_lenth = $column->getLength();
        $this->_isSigned = $column->isSigned();

		$this->_isForeignKey = $this->_isForeignKey();
    }

    function getTableCell()
    {
		if ($this->_debug){
			$this->_log->write(__METHOD__ );
		}

        switch ($this->_dataType)
        {
            case "int":
            case "tinyint":
            case "smallint":
            case "mediumint":
            case "int":
            case "bigint":
            case "decimal":
            case "float":
            case "double":
            case "real":
            case "bit":
            case "boolean":
            case "serial":

                return "<td class='text-right'>" . $this->_value . "</td>";
                break;

            /** datevalues */
            case "date":
            case "datetime":
            case "timestamp":
            case "time":
            case "year":

            /** string values */
            case "char":
            case "varchar":
            case "text":
            case "tinytext":
            case "mediumtext":
            case "longtext":
            case "binary":
            case "varbinary":
            case "tinyblob":
            case "mediumblob":
            case "blob":
            case "longblob":
            case "enum":
            case "set":

            default:
                return "<td>" . $this->_value . "</td>";
        }
    }

    function getEditableObject()
    {
		if ($this->_debug){
			$this->_log->write(__METHOD__ );
		}

        print_r($this->_name . " = " . $this->_value . "<br>");

        /** hide the prumary key */
        if ($this->_isPrimaryKey)
        {
            ?>
            <div class="form-group row">
                <label for="<?php echo $this->_name ?>" class="col-sm-2 control-label"></label>
                <div class="col-sm-20">
                    <?php
                        $params = ["class" => "form-control", "name" => $this->_name, "id" => $this->_name, "value"=>$this->_value];
                        echo HtmlField::getHiddenfield($params);
                    ?>
                </div>
            </div>
            <?php
        } 
        elseif(method_exists($this->_tableName, $this->_name)) 
        {
            /** override the default behaviour */
            $class = new $this->_tableName($this->_db);
            echo $class->{$this->_name}($this->_value);
        }
        elseif($this->_isForeignKey()) 
        {
            /* get the pk and the valrep for the referenced table */
            $options = $this->_parentObject->getValueRepresentation();
            ?>
            <div class="form-group row">
                <label for="<?php echo $this->_name ?>" class="col-sm-2 control-label"><?php echo $this->_label; ?></label>
                <div class="col-sm-20">
                    <?php
                        $params = ["class" => "form-control", "name" => $this->_name, "id" => $this->_name, "value"=>$this->_value];
                        echo HtmlField::getDropDown($params, $options);
                    ?>
                </div>
            </div>
            <?php

        }
        else 
        {
            ?>
            <div class="form-group row">
                <label for="<?php echo $this->_name ?>" class="col-sm-2 control-label"><?php echo $this->_label; ?></label>
                <div class="col-sm-20">
            <?php

            switch ($this->_dataType)
            {
                case "tinyint":
                    $isChecked = ($this->_value == 1 ? true : false);

                    $params = ["class" => "form-control", "name" => $this->_name, "id" => $this->_name];
                    echo HtmlField::getCheckbox($params, $isChecked);
                    break;


                case "text":
                case "tinytext":
                case "mediumtext":
                case "longtext":
                    $params = ["name" => $this->_name, "id" => $this->_name, "cols"=>40, "rows"=>5, "value"=>$this->_value];
                    echo HtmlField::getTextarea($params, $this->_isMandatory);
                    break;

                /** numeric values  */
                case "int":
                case "tinyint":
                case "smallint":
                case "mediumint":
                case "int":
                case "bigint":
                case "decimal":
                case "float":
                case "double":
                    $params["min"] = "0";

                default:
                    $params["class"] = "form-control";
                    $params["name"] = $this->_name;
                    $params["id"] = $this->_name;
                    $params["value"] = $this->_value;
                    $params["size"] = $this->_length;
                    echo HtmlField::getTextfield($params, $this->_isMandatory);
                    break;
            }
            ?>
                </div>
            </div>
            <?php
        }
    }

    /** return the sql query value for insert and update */
    function getQueryValue()
    {
		if ($this->_debug){
			$this->_log->write(__METHOD__ );
		}

        if (isset($this->_value))
        {
            if (empty($this->_value))
            {
                if (is_numeric($this->_value))
                {
                    return $this->_value;
                } else {
                    return "NULL";
                }
            } else {
                switch ($this->_dataType)
                {
                    /** datevalues */
                    case "date":
                    case "datetime":
                    case "timestamp":
                    case "time":
                    case "year":

                    /** string values */
                    case "char":
                    case "varchar":
                    case "text":
                    case "tinytext":
                    case "mediumtext":
                    case "longtext":
                    case "binary":
                    case "varbinary":
                    case "tinyblob":
                    case "mediumblob":
                    case "blob":
                    case "longblob":
                    case "enum":
                    case "set":

//                        return "\"" . $this->_db->realEscapeString($this->_value) . "\"";
                        return $this->_value;

                        break;
                    default:
                        /**
                         * tinyint
                         * smallint
                         * mediumint
                         * int
                         * bigint
                         * decimal
                         * float
                         * double
                         * real
                         * bit
                         * boolean
                         * serial
                         */
                        return $this->_value;
                        break;
                }
            }
        } else {
            return "NULL";
        }
    }

    /** is the field part of a foreign key relationship? */
	private function _isForeignKey()
	{
		if ($this->_debug){
			$this->_log->write(__METHOD__ );
		}

        if (!$this->_isForeignKey)
		{
			$sql = "SELECT REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME ";
			$sql .= "FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE ";
			$sql .= "WHERE REFERENCED_TABLE_NAME IS NOT NULL ";
			$sql .= "AND REFERENCED_COLUMN_NAME IS NOT NULL ";
			$sql .= "AND TABLE_SCHEMA = '" . $this->_serverName . "' ";
			$sql .= "AND TABLE_NAME = '" . $this->_tableName . "' ";
			$sql .= "AND COLUMN_NAME	= '" . $this->_name . "'";

            $rows = $this->_db->select($sql);
            if (!empty($rows))
            {
                $this->_parentSchema = $rows[0]['REFERENCED_TABLE_SCHEMA'];
                $this->_parentTable = $rows[0]['REFERENCED_TABLE_NAME'];
                $this->_parentField = $rows[0]['REFERENCED_COLUMN_NAME'];

                $this->_parentObject = new MysqlTable($this->_db, ['TABLE_SCHEMA'=>$this->_parentSchema, 'TABLE_NAME'=>$this->_parentTable]);

                $this->_isForeignKey = true;
            }
		}
		return $this->_isForeignKey;
	}

    function getQueryDataType() : string 
    {
		if ($this->_debug){
			$this->_log->write(__METHOD__ );
		}

        if ($this->_value === "NULL") 
        {
            return "";
        } 
        elseif (is_numeric($this->_value)) 
        {
            return "i";
        } else {
            return "s";
        }
    }

    function getQueryPlaceholder() : string
    {
		if ($this->_debug){
			$this->_log->write(__METHOD__ );
		}

        if ($this->_value != "NULL") 
        {
            return "?";
        } else {
            return $this->_value;
        }
    }
}
?>