<?php
require_once "HtmlField.php";
class MysqlField
{
    private $_value;
    private $_dataType;

    private $_serverName;
    private $_tableName;
    private $_name;
    private $_isPrimaryKey = false;
    private $_isMandatory = false;
    private $_default;
    private $_label;    
    private $_length;
    private $_isSigned = false;    

	/* relationship information */
    private $_isChildInRelationship = false;
    private $_parentSchema;
    private $_parentTable;
    private $_parentDField;

    function __construct(MysqlColumn $properties, $value = null)
    {
        $this->_value = $value;
        $this->_dataType = $properties->getDatatype();

        $this->_serverName = $properties->getServer();
        $this->_tableName = $properties->getTable();

        $this->_name = $properties->getColumn();
        $this->_isPrimaryKey = $properties->getPrimaryKey();
        $this->_isMandatory = $properties->isMandatory();
        $this->_label = $properties->getLabel();
        $this->_default = $properties->getDefault();
        $this->_lenth = $properties->getLength();
        $this->_isSigned = $properties->isSigned();

		$this->_isChildInRelationship = $this->_isChildInRelationship();
    }

    function getTableCell()
    {
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
        } else {
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
        if (isset($this->_value))
        {
            if (empty($this->_value))
            {
                if (is_numeric($this->_value))
                {
                    return $this->_value;
                } else {
                    // print_r($this->_name . "<br/>");
                    // print_r($this->_dataType . "<br/>");
                    // print_r($this->_value . "<br/>");
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

                        return "\"" . $this->_value . "\"";
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
        }
    }

	private function _isChildInRelationship()
	{
		if (!$this->_isChildInRelationship)
		{
			$sql = "SELECT REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME ";
			$sql .= "FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE ";
			$sql .= "WHERE REFERENCED_TABLE_NAME IS NOT NULL ";
			$sql .= "AND REFERENCED_COLUMN_NAME IS NOT NULL ";
			$sql .= "AND TABLE_SCHEMA = '" . $this->_serverName . "'"; 
			$sql .= "AND TABLE_NAME = '" . $this->_tableName . "'";
			$sql .= "AND COLUMN_NAME	= '" . $this->_name . "'";
			print_r($sql);

			$rows = $this->_db->executeQuery($sql);
			print_r($rows);

			//private $_parentSchema;
			//private $_parentTable;
			//private $_parentDField;


			$this->_isChildInRelationship = true;

		
		}
		return $this->_isChildInRelationship;

	}
}
?>