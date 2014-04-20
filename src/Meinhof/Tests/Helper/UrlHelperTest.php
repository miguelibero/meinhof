<?php

namespace Meinhof\Tests\Helper;

use Meinhof\Helper\UrlHelper;

class TestModel
{
    protected $withUrl;

    public function __construct($withUrl)
    {
        $this->withUrl = $withUrl;
    }

    public function getViewUrl()
    {
        if ($this->withUrl) {
            return '{info.slug}.html';
        }
    }

    public function getInfo()
    {
        return array('slug'=>'test');
    }
}

class UrlHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $helper;

    public function setUp()
    {
        $this->helper = new UrlHelper('{prefix}/{locale}/{info.slug}.html', array('prefix'=>'..'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMissingParameters()
    {
        $url = $this->helper->getUrl(null, array());
    }

    public function testParameters()
    {
        $url = $this->helper->getUrl(null, array('locale'=>'es', 'info' => array('slug'=>'page')));
        $this->assertEquals('../es/page.html', $url);
    }

    public function testModel()
    {
        $url = $this->helper->getUrl(new TestModel(true), array());
        $this->assertEquals('test.html', $url);

        $url = $this->helper->getUrl(new TestModel(false), array('locale'=>'en'));
        $this->assertEquals('../en/test.html', $url);
    }
}
