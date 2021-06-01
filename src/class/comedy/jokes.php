<?php
class jokes
{
    protected $_db;
    function __construct(MysqlDatabase $db)
    {
        $this->_db = $db;
    }

    function cdlanguage(string $value = null) : void
    {
        $options = ["" => "Selecteer een waarde", "ENG" => "Engels", "NED"=>"Nederlands"];

        ?>
        <div class="form-group row">
            <label for="cdlanguage" class="col-sm-2 control-label">Taal</label>
            <div class="col-sm-20">
                <?php
                    $params = ["class" => "form-control", "name" => "cdlanguage", "id" => "cdlanguage", "value"=>$value];
                    echo HtmlField::getDropDown($params, $options);
                ?>
            </div>
        </div>
        <?php
    }

    function created_at(string $value = null) : void
    {
        $value = (empty($value) ? date("Y-m-d H:i:s") : $value);
        
        ?>
        <div class="form-group row">
            <label for="created_at" class="col-sm-2 control-label">created_at</label>
            <div class="col-sm-20">
                <input type='text' class='form-control' name='created_at' id='created_at' value='<?php echo $value; ?>' size='20'>
            </div>
        </div>
        <?php
    }

    function updated_by(string $value = null) : void
    {
        $value = (empty($value) ? "ADMIN" : $value);
        
        ?>
        <div class="form-group row">
            <label for="updated_by" class="col-sm-2 control-label">updated_by</label>
            <div class="col-sm-20">
                <input type='text' class='form-control' name='updated_by' id='updated_by' value='<?php echo $value; ?>' size='20'>
            </div>
        </div>
        <?php
    }

    function updated_at(string $value = null) : void
    {
//        $value = (empty($value) ? date("Y-m-d H:i:s") : $value);
        
        ?>
        <div class="form-group row">
            <label for="updated_at" class="col-sm-2 control-label">updated_at</label>
            <div class="col-sm-20">
                <input type='text' class='form-control' name='updated_at' id='updated_at' value='<?php echo $value; ?>' size='20'>
            </div>
        </div>
        <?php
    }}
?>