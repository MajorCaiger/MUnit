<?php

/**
 * Mockery Test Case Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Test\Adapter\Mockery;

use Mockery as m;
use MUnit\Adapter\Mockery\TestCase;

/**
 * Mockery Test Case Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TestCaseTest extends TestCase
{
    public function testSomeMethod()
    {
        $this->describe('someMethod', function() {
            $this->beforeEach(function($scope) {
                $scope->sample = new \MUnit\Test\Resource\Sample();
                $this->mockObject = m::mock();
                $this->mockObject->shouldReceive('doSomething')->once();
            });

            $this->afterEach(function($scope) {
                unset($scope->sample);
                unset($this->mockObject);
            });

            $this->describe('when given a positive mock object', function() {
                $this->beforeEach(function() {
                    $this->mockObject->shouldReceive('checkSomething')->once()
                        ->andReturn(true);
                    $this->mockObject->shouldReceive('doSomethingPositive');
                });

                $this->it('will doSomethingPositive with the mock object', function($scope) {
                    $scope->sample->sampleMethod($this->mockObject);
                });
            });

            $this->describe('when given a negative mock object', function() {
                $this->beforeEach(function() {
                    $this->mockObject->shouldReceive('checkSomething')->once()
                        ->andReturn(false);
                    $this->mockObject->shouldReceive('doSomethingNegative');
                });

                $this->it('will doSomethingNegative with the mock object', function($scope) {
                    $scope->sample->sampleMethod($this->mockObject);
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
                'Failure test should throw exception: Failed asserting that false is true.',
                $e->getMessage()
            );
        }
    }
}
