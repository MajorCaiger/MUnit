<?php

/**
 * Bootstrap the unit tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Test;

/**
 * Bootstrap the unit tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Bootstrap
{
    public static function init()
    {
        require(__DIR__ . '/../vendor/autoload.php');
    }
}

Bootstrap::init();
