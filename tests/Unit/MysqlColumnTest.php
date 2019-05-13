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

    // public function testClassCanBeInstatiated()
    // {
	// 	$object = new $this->_class;

    //     $this->assertInstanceOf($this->_class, new $this->_class);
    // }

    /**
     * @dataProvider datatypeProvider
     */
    public function testConvertDatatypeToFieldLength($datatype, $expected)
    {
        $list = ['TABLE_SCHEMA'=>'some_schema', 'TABLE_NAME'=>'some_table_name', 'COLUMN_NAME'=>'some_column_name', 'COLUMN_COMMENT'=>'some_column_comment'];

        $object = new $this->_class($list);

        $this->assertSame($object->getLength($datatype), $expected);
    }


    public function datatypeProvider()
    {
        return [
            ["TINYINT", 4],
            ["SMALLINT", 6],
            ["INT", 11],
            ["BIGINT", 20],
            ["", 40]            
        ];
    }    
}
?>