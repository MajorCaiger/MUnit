<?php

/**
 * MUnit Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Adapter\Mockery;

use MUnit\Adapter\TestCaseTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * MUnit Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TestCase extends MockeryTestCase
{
    use TestCaseTrait;
}
