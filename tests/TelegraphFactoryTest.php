<?php
namespace Telegraph;

use Traversable;
use PHPUnit\Framework\TestCase;

class TelegraphFactoryTest extends TestCase
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
        $queue = $this->createMock(Traversable::class);

        $telegraph = $this->telegraphFactory->newInstance($queue);
        $this->assertInstanceOf('Telegraph\Telegraph', $telegraph);
    }

    public function testInvalidQueue()
    {
        $this->expectException(
            'Telegraph\Exception',
            'The middleware queue must be an array or a Traversable.'
        );

        $this->telegraphFactory->newInstance('bad argument');
    }
}
