<?php

namespace Meinhof\Tests\Templating\Storage;

use Symfony\Component\Templating\Storage\Storage;
use Meinhof\Templating\Storage\MatterStorage;
use Meinhof\Templating\Storage\FrontMatterStorage;

class FrontMatterStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $content;

    public function setUp()
    {
        $storage = $this->getMockBuilder('Symfony\\Component\\Templating\\Storage\\Storage')
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects($this->any())
            ->method('getContent')
            ->will($this->returnCallback(array($this, 'getContent')));

        $this->content = "---\nmatter\n---\ncontent";
        $this->storage = new FrontMatterStorage($storage);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function testImplementation()
    {
        $this->assertTrue($this->storage instanceof Storage);
        $this->assertTrue($this->storage instanceof MatterStorage);
    }


    public function testLoad()
    {
        $this->assertEquals("matter", $this->storage->getMatter());
        $this->assertEquals("content", $this->storage->getContent());
    }    
}