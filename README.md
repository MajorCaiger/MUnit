MUnit
=====
MUnit is a small library that wraps PHPUnit. The library allows you to write "Jasmine" style tests for your php applications, while still giving you the power of PHPUnit and Mockery. It contains 2 abstract test cases, 1 that extends PHPUnit_Framework_TestCase and 1 that extends MockeryTestCase.

### Installation
#### Via composer
    "require": {
        "major-caiger/munit": "~0.1.0"
    }

#### Sample class
    <?php
    
    namespace MUnit\Test\Resource;
    
    class Sample
    {
        public function sampleMethod($object)
        {
            $object->doSomething();
    
            if ($object->checkSomething()) {
                $object->doSomethingPositive();
            } else {
                $object->doSomethingNegative();
            }
        }
    }
    
#### Sample test case (Using the PHPUnit adapter)
    <?php
    
    namespace MUnit\Test\Adapter\PhpUnit;
    
    use MUnit\Adapter\PhpUnit\TestCase;
    
    class TestCaseTest extends TestCase
    {
        public function testSomeMethod()
        {
            $this->describe('someMethod', function() {
                $this->beforeEach(function() {
                    $this->sample = new \MUnit\Test\Resource\Sample();
                    $this->mockObject = $this->getMock(
                        '\stdClass',
                        array(
                            'doSomething',
                            'checkSomething',
                            'doSomethingPositive',
                            'doSomethingNegative'
                        )
                    );
                    $this->mockObject->expects($this->once())
                        ->method('doSomething');
                });
    
                $this->afterEach(function() {
                    unset($this->sample);
                    unset($this->mockObject);
                });
    
                $this->describe('when given a positive mock object', function() {
                    $this->beforeEach(function() {
                        $this->mockObject->expects($this->once())
                            ->method('checkSomething')
                            ->willReturn(true);
                        $this->mockObject->expects($this->once())
                            ->method('doSomethingPositive');
                    });
    
                    $this->it('will doSomethingPositive with the mock object', function() {
                        $this->sample->sampleMethod($this->mockObject);
                    });
                });
    
                $this->describe('when given a negative mock object', function() {
                    $this->beforeEach(function() {
                        $this->mockObject->expects($this->once())
                            ->method('checkSomething')
                            ->willReturn(false);
                        $this->mockObject->expects($this->once())
                            ->method('doSomethingNegative');
                    });
    
                    $this->it('will doSomethingNegative with the mock object', function() {
                        $this->sample->sampleMethod($this->mockObject);
                    });
                });
            });
        }
    }

#### Sample test case (Using the Mockery adapter)
    <?php
    
    namespace MUnit\Test\Adapter\Mockery;
    
    use Mockery as m;
    use MUnit\Adapter\Mockery\TestCase;
    
    class TestCaseTest extends TestCase
    {
        public function testSomeMethod()
        {
            $this->describe('someMethod', function() {
                $this->beforeEach(function() {
                    $this->sample = new \MUnit\Test\Resource\Sample();
                    $this->mockObject = m::mock();
                    $this->mockObject->shouldReceive('doSomething')->once();
                });
    
                $this->afterEach(function() {
                    unset($this->sample);
                    unset($this->mockObject);
                });
    
                $this->describe('when given a positive mock object', function() {
                    $this->beforeEach(function() {
                        $this->mockObject->shouldReceive('checkSomething')->once()
                            ->andReturn(true);
                        $this->mockObject->shouldReceive('doSomethingPositive');
                    });
    
                    $this->it('will doSomethingPositive with the mock object', function() {
                        $this->sample->sampleMethod($this->mockObject);
                    });
                });
    
                $this->describe('when given a negative mock object', function() {
                    $this->beforeEach(function() {
                        $this->mockObject->shouldReceive('checkSomething')->once()
                            ->andReturn(false);
                        $this->mockObject->shouldReceive('doSomethingNegative');
                    });
    
                    $this->it('will doSomethingNegative with the mock object', function() {
                        $this->sample->sampleMethod($this->mockObject);
                    });
                });
            });
        }
    }

#### Known issues
- At the moment you need to declare the <strong>beforeEach</strong> and <strong>afterEach</strong> callbacks before any further nested calls to <strong>describe</strong>

#### Support
- PHP 5.4+