<?php

use PHPUnit\Framework\TestCase;
use Srdestino\Waitable\IWaitable;
use Srdestino\Waitable\Waitable;

class WaitableTest extends TestCase
{
    public function testShouldGetValueFromWait(){
        $value = "Hello world";
        $waitable = new Waitable(function (IWaitable $waitable) use($value){
            $waitable->Resolve($value);
        });
        $result = $waitable->Wait();
        $this->assertEquals($result,$value);
    }

    public function testShouldGetObjectFromWait(){
        //Arrange
        $value = new TestClass();
        $value->SetValue("Hello world");

        //Act
        $waitable = new Waitable(function (IWaitable $waitable) use($value){
            $waitable->Resolve($value);
        });

        //Assert
        $result = $waitable->Wait();
        $this->assertEquals($value,$result);
    }

    public function testShouldGetArrayFromWait(){
        //Arrange
        $value = [1,2,3];

        //Act
        $waitable = new Waitable(function (IWaitable $waitable) use($value){
            $waitable->Resolve($value);
        });
        $result = $waitable->Wait();

        //Assert
        $this->assertEquals($result,$value);
    }

    public function testShouldGetAllResultsFromWaitAll(){
        //Arrenge
        $firstValue = "First value";
        $secondValue = "Second value";

        //Act
        $firstWaitable = new Waitable(function (IWaitable $waitable) use($firstValue){
            $waitable->Resolve($firstValue);
        });
        $secondWaitable = new Waitable(function (IWaitable $waitable) use($secondValue){
            $waitable->Resolve($secondValue);
        });
        list($firstResult, $secondResult) = Waitable::WaitAll([$firstWaitable, $secondWaitable]);

        //Assert
        $this->assertEquals($firstResult,$firstValue);
        $this->assertEquals($secondResult,$secondValue);
    }

    public function testShouldRunAsync(){

        //Arrange
        $startTime = microtime(true);

        //Act
        $firstWaitable = new Waitable(function (IWaitable $waitable){
            sleep(3);
            $waitable->Resolve(null);
        });
        $secondWaitable = new Waitable(function (IWaitable $waitable){
            sleep(5);
            $waitable->Resolve(null);
        });
        Waitable::WaitAll([$firstWaitable, $secondWaitable]);
        $total = ((int)microtime(true) . "\n") - $startTime;

        //Assert
        $this->assertTrue($total >= 4 && $total <= 6);
    }
}

class TestClass
{
    private $value;
    public function GetValue(): string{
        return $this->value;
    }
    public function SetValue($value){
        $this->value = $value;
    }
}
