<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ClubTest extends TestCase
{
    public function testClassClubExists()
    {
        $this->assertTrue(class_exists("Club"));
    }

    public function testClassHasFunctionGetPhotoCollectionExists()
    {
        $this->assertTrue(method_exists("Club", '_getPhotoCollection'));
    }

//    public function testPhotoCollectionReturnsEmpty()
//    {
//        $object = new Club(59);

//        $database = $this->getMockBuilder('Database')
//            ->setConstructorArgs()
//            ->getMock();
//        $database->expects($this->once())
//            ->method('queryDB')
//            ->will($this->returnvalue([]));

//        /**stubbing the queryDb */
////        $object->expects($this->once())
////            ->method('queryDB')
////            ->will($this->returnvalue([]));

//        $items = $this->invokeMethod($object, '_getPhotoCollection', [59, $database]);
//    }

//    public function testPhotoCollectionContainsElements()
//    {
//        $object = new Club(59);
//        $items = $this->invokeMethod($object, '_getPhotoCollection', []);

//        $this->assertTrue(count($items) > 0);
//    }

    /** testing private classes  */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
    
        return $method->invokeArgs($object, $parameters);
    }    
}
?>