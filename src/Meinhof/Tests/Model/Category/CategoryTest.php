<?php

namespace Meinhof\Tests\Model\Category;

use Meinhof\Model\Category\Category;
use Meinhof\Model\Category\CategoryInterface;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    protected $category;

    public function setUp()
    {
        $this->category = new Category('key', 'Category Name', 'category-slug');
    }

    public function testImplementation()
    {
        $this->assertTrue($this->category instanceof CategoryInterface);
    }

    public function testName()
    {
        $this->assertEquals('key', $this->category->getKey());
        $this->assertEquals('category-slug', $this->category->getSlug());
        $this->assertEquals('Category Name', $this->category.'');
    }
}
