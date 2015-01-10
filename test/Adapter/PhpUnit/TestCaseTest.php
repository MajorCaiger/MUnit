<?php

/**
 * PhpUnit Test Case Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Test\Adapter\PhpUnit;

use MUnit\Adapter\PhpUnit\TestCase;

/**
 * PhpUnit Test Case Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
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
