<?php

/**
 * MUnit Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Adapter\PhpUnit;

use MUnit\Adapter\TestCaseTrait;
use PHPUnit_Framework_TestCase;

/**
 * MUnit Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    use TestCaseTrait;
}
