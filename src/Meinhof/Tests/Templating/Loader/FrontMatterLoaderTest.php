<?php

namespace Meinhof\Tests\Templating;

use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Storage\StringStorage;
use Symfony\Component\Templating\TemplateReference;

use Meinhof\Templating\Loader\FrontMatterLoader;
use Meinhof\Templating\Storage\MatterStorage;


class FrontMatterLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;
    protected $fresh = true;
    protected $storage;

    public function setUp()
    {
        $loader = $this->getMock('Symfony\\Component\\Templating\\Loader\\LoaderInterface');

        $this->storage = new StringStorage("***\nmatter\n***\ntemplate");

        $loader->expects($this->any())
            ->method('load')
            ->will($this->returnCallback(array($this, 'getStorage')));

        $loader->expects($this->any())
            ->method('isFresh')
            ->will($this->returnCallback(array($this, 'getIsFresh')));   

        $this->loader = new FrontMatterLoader($loader, '/(^|\n)\*{3,}\n/');
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function getIsFresh()
    {
        return $this->fresh;
    }

    public function testImplementation()
    {
        $this->assertTrue($this->loader instanceof LoaderInterface);
    }

    public function testLoad()
    {
        $template = new TemplateReference('name', 'engine');
        $storage = $this->loader->load($template);

        $this->assertTrue($storage instanceof MatterStorage);

        $this->assertEquals('matter', $storage->getMatter());
        $this->assertEquals('template', $storage->getContent());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testStorageFail()
    {
        $this->storage = null;
        $template = new TemplateReference('name', 'engine');
        $this->loader->load($template);
    }

    public function testIsFresh()
    {
        $template = new TemplateReference('name', 'engine');
        $time = time();     

        $this->assertTrue($this->loader->isFresh($template, $time));

        $this->fresh = false;

        $this->assertFalse($this->loader->isFresh($template, $time));
    }  
}