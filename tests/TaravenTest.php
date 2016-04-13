<?php
use Taraven\Taraven;

class TaravenTest extends PHPUnit_Framework_TestCase
{
    public function testTaravenHello()
    {
        $taraven = new Taraven;
        $this->assertTrue($taraven->hello());
    }
}