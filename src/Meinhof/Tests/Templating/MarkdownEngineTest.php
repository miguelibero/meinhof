<?php

namespace Meinhof\Tests\Templating;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Storage\Storage;

use Meinhof\Templating\MarkdownEngine;

class MarkdownEngineTest extends \PHPUnit_Framework_TestCase
{
    protected $engine;

    public function setUp()
    {
        $parser = $this->getMock('Symfony\\Component\\Templating\\TemplateNameParserInterface');
        $template = $this->getMock('Symfony\\Component\\Templating\\TemplateReferenceInterface');

        $template->expects($this->any())
            ->method('get')
            ->with($this->equalTo('name'))
            ->will($this->returnValue('test.markdown'));         

        $template->expects($this->any())
            ->method('all')
            ->will($this->returnValue(array('key'=>'test')));

        $parser->expects($this->any())
            ->method('parse')
            ->with($this->equalTo('test.markdown'))
            ->will($this->returnValue($template));

        $loader = new FilesystemLoader(array(__DIR__.'/../Fixtures/%key%.markdown'));

        $this->engine = new MarkdownEngine($parser, $loader);
    }

    public function testImplementation()
    {
        $this->assertTrue($this->engine instanceof EngineInterface);
    }

    public function testRender()
    {
        $template = $this->getMock('Symfony\\Component\\Templating\\TemplateReferenceInterface');

        $html = $this->engine->render('test.markdown');
        $expect = file_get_contents(__DIR__.'/../Fixtures/test.markdown.html');
        $this->assertEquals($expect, $html);
    }

    public function testSupports()
    {
        $parser = $this->getMock('Symfony\\Component\\Templating\\TemplateNameParserInterface');
        $template = $this->getMock('Symfony\\Component\\Templating\\TemplateReferenceInterface');

        $template->expects($this->any())
            ->method('get')
            ->with($this->equalTo('engine'))
            ->will($this->returnValue('markdown'));         

        $parser->expects($this->any())
            ->method('parse')
            ->with($this->equalTo('test.markdown'))
            ->will($this->returnValue($template));

        $loader = new FilesystemLoader(array(__DIR__.'/../Fixtures/%key%.markdown'));

        $engine = new MarkdownEngine($parser, $loader);

        $this->assertTrue($engine->supports('test.markdown'));
    }    
}