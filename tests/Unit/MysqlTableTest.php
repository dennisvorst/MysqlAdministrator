<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlDatabaseTest extends TestCase
{
    private $_class = "MysqlTable";

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_class));
    }
}
?>