<?php

namespace Meinhof\Tests\Model\Category;

use Meinhof\Model\Category\Category;
use Meinhof\Model\Category\CategoryInterface;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    protected $category;

    public function setUp()
    {
        $this->category = new Category('name');
    }

    public function testImplementation()
    {
        $this->assertTrue($this->category instanceof CategoryInterface);
    }

    public function testName()
    {
        $this->assertEquals('name', $this->category->getName());
        $this->assertEquals('name', $this->category.'');
    }
}
