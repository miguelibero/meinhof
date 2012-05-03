<?php

namespace Meinhof\Tests\Templating;

use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\Storage\Storage;

use Meinhof\Templating\Loader\FrontMatterLoader;


class FrontMatterLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;

    public function setUp()
    {
        $loader = $this->getMock('Symfony\\Component\\Templating\\Loader\\LoaderInterface');
        $this->loader = new FrontMatterLoader($loader);
    }

    public function testImplementation()
    {
        $this->assertTrue($this->loader instanceof LoaderInterface);
    }
}