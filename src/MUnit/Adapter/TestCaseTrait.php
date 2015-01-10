<?php

/**
 * MUnit Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Adapter;

/**
 * MUnit Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait TestCaseTrait
{
    /**
     * Stack nested descriptions
     *
     * @var array
     */
    protected $descriptions = [];

    /**
     * Stack nested beforeEach callbacks
     *
     * @var array
     */
    protected $beforeCallbacks = [];

    /**
     * Stack afterEach callbacks
     *
     * @var array
     */
    protected $afterCallbacks = [];

    /**
     * Stack nested tests
     *
     * @var array
     */
    protected $tests = [];

    /**
     * Describe and setup the test suite
     *
     * @param string $description
     * @param callable $callback
     */
    protected function describe($description, $callback)
    {
        $this->setUpSuite($description);

        $callback();
        $this->runStackedTests();

        $this->tearDownSuite();
    }

    /**
     * Set up the test suite
     *
     * @param string $description
     */
    protected function setUpSuite($description)
    {
        $this->descriptions[] = $description;
        $this->beforeCallbacks[] = [];
        $this->afterCallbacks[] = [];
        $this->tests[] = [];
    }

    /**
     * Tear down the test suite
     */
    protected function tearDownSuite()
    {
        array_pop($this->descriptions);

        unset($this->beforeCallbacks[$this->getLastIndex($this->beforeCallbacks)]);
        unset($this->afterCallbacks[$this->getLastIndex($this->afterCallbacks)]);
        unset($this->tests[$this->getLastIndex($this->tests)]);
    }

    /**
     * Get the last index of an array
     *
     * @param array $array
     * @return int
     */
    protected function getLastIndex(array $array)
    {
        end($array);
        $index = key($array);

        return $index;
    }

    /**
     * Add a before each callback to the stack
     *
     * @param callable $callback
     */
    protected function beforeEach($callback)
    {
        $this->beforeCallbacks[$this->getLastIndex($this->beforeCallbacks)][] = $callback;
    }

    /**
     * Add an after each callback to the stack
     *
     * @param callable $callback
     */
    protected function afterEach($callback)
    {
        $this->afterCallbacks[$this->getLastIndex($this->afterCallbacks)][] = $callback;
    }

    /**
     * Add a test to the stack
     *
     * @param string $description
     * @param callable $callback
     */
    protected function it($description, $callback)
    {
        $this->tests[$this->getLastIndex($this->tests)][] = array(
            'description' => $description,
            'callback' => $callback
        );
    }

    /**
     * Run the stacked tests
     */
    protected function runStackedTests()
    {
        $tests = $this->tests[$this->getLastIndex($this->tests)];

        foreach ($tests as $testDetails) {

            $this->setUpTest();

            $this->runStackedTest($testDetails['description'], $testDetails['callback']);

            $this->tearDownTest();
        }
    }

    /**
     * Run an individual test
     *
     * @param string $description
     * @param callable $test
     * @throws \PHPUnit_Framework_Exception
     */
    protected function runStackedTest($description, $test)
    {
        try {
            $test();
        } catch (\PHPUnit_Framework_Exception $e) {

            $exeptionType = get_class($e);

            // Tweak the exception message to diplay the test description
            throw new $exeptionType(
                implode(' ', $this->descriptions) . ' ' . $description . ': ' . $e->getMessage()
            );
        }
    }

    /**
     * Call all stacked beforeEach functions
     */
    protected function setUpTest()
    {
        foreach ($this->beforeCallbacks as $callbacks) {
            foreach ($callbacks as $callback) {
                $callback();
            }
        }
    }

    /**
     * Call all stacked afterEach functions
     */
    protected function tearDownTest()
    {
        foreach ($this->afterCallbacks as $callbacks) {
            foreach ($callbacks as $callback) {
                $callback();
            }
        }
    }
}
