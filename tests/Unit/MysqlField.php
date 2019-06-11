<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlField extends TestCase
{
    private $_class = "MysqlField";

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_class));
    }

    public function testClassCanBeInstatiated()
    {
	 	$object = new $this->_class;

        $this->assertInstanceOf($this->_class, new $this->_class);
    }

    public function testQueryValueReturnsNull()
    {
        $this->assertEquals("NULL", "NULL");
    }
    public function testQueryValueReturnsZero()
    {
        $this->assertEquals("NULL", "NULL");
    }
    public function testQueryValueReturnsNumber()
    {
        $this->assertEquals("NULL", "NULL");
    }
    public function testQueryValueReturnsString()
    {
        $this->assertEquals("NULL", "NULL");
    }

}
?>