<?php
namespace Telegraph;

use Traversable;

class TelegraphFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $telegraphFactory;

    protected function setUp()
    {
        $this->telegraphFactory = new TelegraphFactory();
    }

    public function testArray()
    {
        $queue = [];
        $telegraph = $this->telegraphFactory->newInstance($queue);
        $this->assertInstanceOf('Telegraph\Telegraph', $telegraph);
    }

    public function testTraversable()
    {
        $queue = $this->getMock(Traversable::class);

        $telegraph = $this->telegraphFactory->newInstance($queue);
        $this->assertInstanceOf('Telegraph\Telegraph', $telegraph);
    }

    public function testInvalidQueue()
    {
        $this->setExpectedException(
            'Telegraph\Exception',
            'The middleware queue must be an array or a Traversable.'
        );

        $this->telegraphFactory->newInstance('bad argument');
    }
}
