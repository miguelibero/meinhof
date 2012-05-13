<?php

namespace Meinhof\Tests\Model\Category;

use Meinhof\Model\Page\Page;
use Meinhof\Model\Page\PageInterface;

class PageTest extends \PHPUnit_Framework_TestCase
{
    protected $page;

    public function setUp()
    {
        $key = 'new_page';
        $slug = 'new_page';
        $updated = 1;
        $title = 'New page';
        $view = 'view';
        $info = array('key'=>'value');
        $this->page = new Page($key, $slug, $updated, $title, $view, $info);
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

    public function testFromArray()
    {
        $page = Page::fromArray(array(
            'key'       => 'new_page',
            'updated'   => 'september 11 2001',
        ));
        $this->assertTrue($page instanceof PageInterface);

        $date = new \DateTime('september 11 2001');
        $this->assertEquals($date, $page->getUpdated());

        $this->assertEquals('new_page', $page->getViewTemplatingKey());
    }
}