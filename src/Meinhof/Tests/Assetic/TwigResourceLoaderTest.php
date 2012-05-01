<?php

namespace Meinhof\Tests\Assetic;

use Meinhof\Assetic\ResourceLoaderInterface;
use Meinhof\Assetic\TwigResourceLoader;

class TwigResourceLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;

    public function setUp()
    {
        $twig_loader = $this->getMock('Twig_LoaderInterface');
        $this->loader = new TwigResourceLoader($twig_loader);
    }   

    public function testImplementation()
    {
        $this->assertTrue($this->loader instanceof ResourceLoaderInterface);
    }

    public function testLoad()
    {
        $manager = $this->getMockBuilder('Assetic\\Factory\\LazyAssetManager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->once())
            ->method('addResource')
            ->with($this->equalTo('test'), $this->equalTo('twig'));

        $this->loader->load('test', $manager);
    }    
}