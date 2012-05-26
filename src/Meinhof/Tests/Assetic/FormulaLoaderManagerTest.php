<?php

namespace Meinhof\Tests\Assetic;

use Meinhof\Assetic\FormulaLoaderManagerInterface;
use Meinhof\Assetic\FormulaLoaderManager;

class FormulaLoaderManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->loader = $this->getMock('Assetic\Factory\Loader\FormulaLoaderInterface');

        $this->manager = new FormulaLoaderManager(array(
            'twig'  => $this->loader
        ));
    }

    public function testImplementation()
    {
        $this->assertTrue($this->manager instanceof FormulaLoaderManagerInterface);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadConstructor()
    {
        $manager = new FormulaLoaderManager(array(
            'not_a_loader'
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotFoundLoader()
    {
        $this->manager->getLoader('not_found');
    }

    public function testGettingLoader()
    {
        $this->assertEquals($this->loader, $this->manager->getLoader('twig'));
        $this->assertEquals(array('twig'), $this->manager->getTypes());
    }
}
