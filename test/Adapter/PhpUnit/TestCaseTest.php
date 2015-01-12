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

    public function testFailuresThrowExceptions()
    {
        try {

            $this->describe('Failure test', function() {
                $this->it('should throw exception', function() {
                    $this->assertTrue(false);
                });
            });

        } catch (\PHPUnit_Framework_Exception $e) {
            $this->assertEquals(
                '(FAILED) Failure test should throw exception: Failed asserting that false is true.',
                $e->getMessage()
            );
        }
    }

    public function testAllFailuresThrowExceptions()
    {
        try {

            $this->describe('test', function() {
               $this->describe('with failures', function() {
                    $this->it('should throw exception', function() {
                        $this->assertTrue(false);
                    });
                });

                $this->describe('with skips', function() {
                    $this->it('should throw exception', function() {
                        $this->markTestSkipped('foo');
                    });
                });

                $this->describe('when incomplete', function() {
                    $this->it('should throw exception', function() {
                        $this->markTestIncomplete('bar');
                    });
                });
            });

        } catch (\Exception $e) {
            $this->assertEquals(
                '(FAILED) test with failures should throw exception: Failed asserting that false is true.
(INCOMPLETE) test when incomplete should throw exception: bar
(SKIPPED) test with skips should throw exception: foo',
                $e->getMessage()
            );
        }
    }
}
