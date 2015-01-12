<?php

/**
 * Test Case Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Adapter;

/**
 * Test Case Trait
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

    protected $scope;
    protected $fail = [];
    protected $incomplete = [];
    protected $risky = [];
    protected $skipped = [];
    protected $error = [];
    protected $depth = 0;

    /**
     * Describe and setup the test suite
     *
     * @todo Need to stack these before running tests, so that we don't need to rely on the order of afterEach and
     * beforeEach calls
     *
     * @param string $description
     * @param callable $callback
     */
    protected function describe($description, $callback)
    {
        $this->depth++;
        if ($this->scope === null) {
            $this->scope = new \stdClass();
        }

        $this->setUpSuite($description);

        $callback($this->scope);
        $this->depth--;

        $this->runStackedTests();

        $this->tearDownSuite();

        if ($this->depth === 0) {
            $this->reportErrors();
        }
    }

    private function reportErrors()
    {
        if (count($this->error) > 0) {
            throw new \Exception($this->getOutputText());
            //throw new \PHPUnit_Framework_Error($this->getOutputText());
        }

        if (count($this->fail) > 0) {
            throw new \PHPUnit_Framework_AssertionFailedError($this->getOutputText());
        }

        if (count($this->risky) > 0) {
            throw new \PHPUnit_Framework_RiskyTestError($this->getOutputText());
        }

        if (count($this->incomplete) > 0) {
            throw new \PHPUnit_Framework_IncompleteTestError($this->getOutputText());
        }

        if (count($this->incomplete) > 0) {
            throw new \PHPUnit_Framework_SkippedTestError($this->getOutputText());
        }
    }

    private function getOutputText()
    {
        $messages = array();

        if (!empty($this->error)) {
            $messages[] = implode("\n", $this->error);
        }

        if (!empty($this->fail)) {
            $messages[] = implode("\n", $this->fail);
        }

        if (!empty($this->risky)) {
            $messages[] = implode("\n", $this->risky);
        }

        if (!empty($this->incomplete)) {
            $messages[] = implode("\n", $this->incomplete);
        }

        if (!empty($this->skipped)) {
            $messages[] = implode("\n", $this->skipped);
        }

        return implode("\n", $messages);
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
     * Set up the test suite
     *
     * @param string $description
     */
    private function setUpSuite($description)
    {
        $this->descriptions[] = $description;
        $this->beforeCallbacks[] = [];
        $this->afterCallbacks[] = [];
        $this->tests[] = [];
    }

    /**
     * Tear down the test suite
     */
    private function tearDownSuite()
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
    private function getLastIndex(array $array)
    {
        end($array);
        $index = key($array);

        return $index;
    }

    /**
     * Run the stacked tests
     */
    private function runStackedTests()
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
    private function runStackedTest($description, $test)
    {
        $errorDescription = implode(' ', $this->descriptions) . ' ' . $description;

        try {
            $test($this->scope);
        } catch (\PHPUnit_Framework_SkippedTestError $e) {
            $this->skipped[] = '(SKIPPED) ' . $errorDescription . ': ' . $e->getMessage();
        } catch (\PHPUnit_Framework_IncompleteTestError $e) {
            $this->incomplete[] = '(INCOMPLETE) ' . $errorDescription . ': ' . $e->getMessage();
        } catch (\PHPUnit_Framework_RiskyTestError $e) {
            $this->risky[] = '(RISKY) ' . $errorDescription . ': ' . $e->getMessage();
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $this->fail[] =  '(FAILED) ' . $errorDescription . ': ' . $e->getMessage();
        } catch (\PHPUnit_Framework_Exception $e) {
            $this->error[] = '(ERROR) ' . $errorDescription . ': ' . $e->getMessage();
        }
    }

    /**
     * Call all stacked beforeEach functions
     */
    private function setUpTest()
    {
        foreach ($this->beforeCallbacks as $callbacks) {
            foreach ($callbacks as $callback) {
                $callback($this->scope);
            }
        }
    }

    /**
     * Call all stacked afterEach functions
     */
    private function tearDownTest()
    {
        foreach ($this->afterCallbacks as $callbacks) {
            foreach ($callbacks as $callback) {
                $callback($this->scope);
            }
        }
    }
}
