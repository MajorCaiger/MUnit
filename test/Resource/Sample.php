<?php

/**
 * Sample
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace MUnit\Test\Resource;

/**
 * Sample
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
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
