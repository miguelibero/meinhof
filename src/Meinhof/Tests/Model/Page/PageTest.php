<?php

namespace Meinhof\Tests\Model\Category;

use Meinhof\Model\Page\Page;
use Meinhof\Model\Page\PageInterface;

class PageTest extends \PHPUnit_Framework_TestCase
{
    protected $page;

    public function setUp()
    {
        $slug = 'new_page';
        $updated = 1;
        $title = 'New page';
        $view = 'view';
        $info = array('key'=>'value');
        $this->page = new Page($slug, $updated, $title, $view, $info);
    }

    public function testImplementation()
    {
        $this->assertTrue($this->page instanceof PageInterface);
    }

    public function testAttributes()
    {
        $this->assertEquals('new_page', $this->page->getSlug());
        $this->assertEquals('New page', $this->page->getTitle());
        $this->assertEquals('view', $this->page->getViewTemplatingKey());
        $this->assertEquals(array('key'=>'value'), $this->page->getInfo());


        $date = new \DateTime();
        $date->setTimestamp(1);
        $this->assertEquals($date, $this->page->getUpdated());
    }    
}