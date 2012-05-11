<?php

namespace Meinhof\Tests\Templating\Twig;

use Meinhof\Helper\UrlHelperInterface;
use Meinhof\Templating\Twig\UrlExtension;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

class UrlExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    public function setUp()
    {
        $helper = $this->getMock('Meinhof\\Helper\\UrlHelperInterface');

        $this->post = $this->getMock('Meinhof\\Model\\Post\\PostInterface');
        $this->page = $this->getMock('Meinhof\\Model\\Page\\PageInterface');

        $helper->expects($this->any())
            ->method('getPostUrl')
            ->with($this->equalTo($this->post))
            ->will($this->returnValue('post_url'));

        $helper->expects($this->any())
            ->method('getPageUrl')
            ->with($this->equalTo($this->page))
            ->will($this->returnValue('page_url'));

        $this->extension = new UrlExtension($helper);
    }

    public function testImplementation()
    {
        $this->assertTrue($this->extension instanceof \Twig_Extension);
        $this->assertEquals('url', $this->extension->getName());
        
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUrlFail()
    {
        $this->extension->getUrl('not_valid');
    }

    public function testGetUrl()
    {
        $functions = $this->extension->getFunctions();
        $this->assertEquals(array('url'), array_keys($functions));
        $get_url = $functions['url'];
        $this->assertTrue($get_url instanceof \Twig_Function);
        
        $url = $this->extension->getUrl($this->post);
        $this->assertEquals('post_url', $url);

        $url = $this->extension->getUrl($this->page);
        $this->assertEquals('page_url', $url);
    }
}