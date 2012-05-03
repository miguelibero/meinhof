<?php

namespace Meinhof\Tests\Templating;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Storage\Storage;

class AbstractEngineTest extends \PHPUnit_Framework_TestCase
{
    protected $engine;
    protected $loader;
    protected $parser;

    public function setUp()
    {
        $this->parser = $this->getMock('Symfony\\Component\\Templating\\TemplateNameParserInterface');
        $template = $this->getMock('Symfony\\Component\\Templating\\TemplateReferenceInterface');
        $this->loader = $this->getMock('Symfony\\Component\\Templating\\Loader\\LoaderInterface');

        $storage = $this->getMockBuilder('Symfony\\Component\\Templating\\Storage\\Storage')
                    ->disableOriginalConstructor()
                    ->getMock();

        $template->expects($this->any())
            ->method('get')
            ->with($this->equalTo('name'))
            ->will($this->returnValue('test'));         

        $template->expects($this->any())
            ->method('all')
            ->will($this->returnValue(array('key'=>'test')));

        $this->parser->expects($this->any())
            ->method('parse')
            ->will($this->returnValue($template));

        $this->loader->expects($this->any())
            ->method('load')
            ->with($this->equalTo($template))
            ->will($this->returnValue($storage));            

        $this->engine = $this->getMockBuilder('Meinhof\\Templating\\AbstractEngine')
            ->setConstructorArgs(array($this->parser, $this->loader))
            ->getMockForAbstractClass();

        $this->engine->expects($this->any())
            ->method('parse')
            ->will($this->returnValue(false));
    }

    public function testImplementation()
    {
        $this->assertTrue($this->engine instanceof EngineInterface);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRenderFail()
    {
        $this->engine->render('test');
    }    

    public function testExists()
    {
        $this->assertTrue($this->engine->exists('test'));

        $this->loader = $this->getMock('Symfony\\Component\\Templating\\Loader\\LoaderInterface');

        $this->loader->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));

        $this->engine = $this->getMockBuilder('Meinhof\\Templating\\AbstractEngine')
            ->setConstructorArgs(array($this->parser, $this->loader))
            ->getMockForAbstractClass();    

        $this->assertFalse($this->engine->exists('test2'));

        $this->parser = $this->getMock('Symfony\\Component\\Templating\\TemplateNameParserInterface');

        $this->parser->expects($this->any())
            ->method('parse')
            ->will($this->returnValue(null));        

        $this->engine = $this->getMockBuilder('Meinhof\\Templating\\AbstractEngine')
            ->setConstructorArgs(array($this->parser, $this->loader))
            ->getMockForAbstractClass();            

        $this->assertFalse($this->engine->exists('test3'));
    }

    public function testStorageCache()
    {
        $this->loader->expects($this->once())
            ->method('load');

        $this->assertTrue($this->engine->exists('test'));
        $this->assertTrue($this->engine->exists('test'));

        try{
            $this->engine->render('test');
        }catch(\Exception $e){
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLoadFail()
    {
        $this->loader->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));

        $this->engine->render('test2');
    }
}