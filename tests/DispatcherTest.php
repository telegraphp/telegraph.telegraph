<?php
namespace Telegraph;

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutResolver()
    {
        FakeMiddleware::$count = 0;

        $queue = [
            new FakeMiddleware(),
            new FakeMiddleware(),
            new FakeMiddleware(),
            function ($request, $next) {
                return new Response();
            },
        ];

        $dispatcher = new Dispatcher($queue);
        $response = $dispatcher->dispatch(ServerRequestFactory::fromGlobals());

        $actual = (string) $response->getBody();
        $this->assertSame('123', $actual);
    }

    public function testWithResolver()
    {
        FakeMiddleware::$count = 0;

        $queue = [
            new FakeMiddleware(),
            new FakeMiddleware(),
            new FakeMiddleware(),
            function ($request, $next) {
                return new Response();
            },
        ];

        $resolver = new FakeResolver();

        $dispatcher = new Dispatcher($queue, $resolver);
        $response = $dispatcher->dispatch(ServerRequestFactory::fromGlobals());

        $actual = (string) $response->getBody();
        $this->assertSame('123', $actual);
    }

    public function testEmptyQueue()
    {
        $resolver = new FakeResolver();
        $queue = [];
        $dispatcher = new Dispatcher($queue, $resolver);
        $this->setExpectedException(
            'Telegraph\Exception',
            'The middleware queue is empty.'
        );
        $dispatcher->dispatch(ServerRequestFactory::fromGlobals());
    }

    public function testNonResponse()
    {
        $queue = [
            function ($request, $next) {
                return 'not a response object';
            },
        ];

        $dispatcher = new Dispatcher($queue);
        $this->setExpectedException(
            'Telegraph\Exception',
            'Middleware must return a response.'
        );
        $dispatcher->dispatch(ServerRequestFactory::fromGlobals());
    }
}
