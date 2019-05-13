<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase
{
    private $_class = "Mysql";

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_class));
    }

    // public function testClassCanBeInstatiated()
    // {
	// 	$object = new $this->_class;

    //     $this->assertInstanceOf($this->_class, new $this->_class);
    // }
}
?>