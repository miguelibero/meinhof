<?php

namespace Meinhof\Tests\Templating;

use Symfony\Component\Templating\TemplateReference;
use Symfony\Component\Templating\Storage\Storage;

use Meinhof\Templating\Loader\FilesystemLoader;

class FilesystemLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;

    public function setUp()
    {
        $this->loader = new FilesystemLoader(array(
            __DIR__.'/../../Fixtures'
        ));
    }

    public function testLoad()
    {
        $template = new TemplateReference('name', 'engine');
        $storage = $this->loader->load($template);

        $this->assertFalse($storage);

        $template = new TemplateReference('test.markdown', 'engine');
        $storage = $this->loader->load($template);
        $this->assertTrue($storage instanceof Storage);
    }

}