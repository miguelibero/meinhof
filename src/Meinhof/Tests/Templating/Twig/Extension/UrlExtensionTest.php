<?php

namespace Meinhof\Tests\Templating\Twig\Extension;

use Meinhof\Helper\UrlHelperInterface;
use Meinhof\Templating\Twig\Extension\UrlExtension;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

class UrlExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    public function setUp()
    {
        $helper = $this->getMock('Meinhof\\Helper\\UrlHelperInterface');

        $this->post = $this->getMock('Meinhof\\Model\\Post\\PostInterface');

        $helper->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo($this->post))
            ->will($this->returnValue('post_url'));

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
        $this->extension->getUrl(1);
    }

    public function testGetUrl()
    {
        $functions = $this->extension->getFunctions();
        $this->assertEquals(array('url'), array_keys($functions));
        $get_url = $functions['url'];
        $this->assertTrue($get_url instanceof \Twig_Function);

        $url = $this->extension->getUrl($this->post);
        $this->assertEquals('post_url', $url);

    }
}
