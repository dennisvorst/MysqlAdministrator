<?php
class MysqlBreadcrumb{
    private $_breadcrumbs = [];
    private $_server;
    private $_table;

    function __construct(MysqlServer $server = null, MysqlTable $table = null)
    {
        $this->_server = $server;
        $this->_table = $table;
    }

    function createBreadcrumb()
    {
        ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
            <?php
            if (empty($this->_table))
            {
                if (empty($this->_server))
                {
                    echo $this->_createMainCrumb(true);
                } else {
                    echo $this->_createMainCrumb(false);
                    echo $this->_server->getBreadCrumb(true);    
                }
            } else {
                echo $this->_createMainCrumb(false);
                echo $this->_server->getBreadCrumb(false);
                echo $this->_table->getBreadCrumb(true);
            }
            ?>
            </ol>
        </nav>
        <?php
    }

    private function _createMainCrumb(bool $isActive = false) : string 
    {
        return "<li class='breadcrumb-item" . ($isActive ? " active" : "' ") . "><a href='controller.php'>Home</a></li>\n";
    }
}
?>