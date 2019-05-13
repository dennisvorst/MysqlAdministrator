<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlColumnTest extends TestCase
{
    private $_class = "MysqlColumn";

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_class));
    }
}
?>