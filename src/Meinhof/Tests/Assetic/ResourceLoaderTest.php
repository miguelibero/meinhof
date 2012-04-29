<?php

namespace Meinhof\Tests\Assetic;

use Meinhof\Assetic\ResourceLoaderInterface;

class ResourceLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementation()
    {
        $stub = $this->getMock('Meinhof\\Assetic\\ResourceLoaderInterface');
        $this->assertTrue($stub instanceof ResourceLoaderInterface);
    }
}