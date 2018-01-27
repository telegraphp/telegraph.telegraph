<?php
namespace Telegraph;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class DispatcherTest extends TestCase
{

    protected function responseFactory()
    {
        return new class implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ) : ResponseInterface {
                return new Response();
            }
        };
    }

    public function testWithoutResolver()
    {
        FakeMiddleware::$count = 0;

        $queue = [
            new FakeMiddleware(),
            new FakeMiddleware(),
            new FakeMiddleware(),
            $this->responseFactory()
        ];

        $dispatcher = new Dispatcher($queue);
        $response = $dispatcher->handle(ServerRequestFactory::fromGlobals());

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
            $this->responseFactory()
        ];

        $resolver = new FakeResolver();

        $dispatcher = new Dispatcher($queue, $resolver);
        $response = $dispatcher->handle(ServerRequestFactory::fromGlobals());

        $actual = (string) $response->getBody();
        $this->assertSame('123', $actual);
    }

    public function testEmptyQueue()
    {
        $resolver = new FakeResolver();
        $queue = [];
        $dispatcher = new Dispatcher($queue, $resolver);
        $this->expectException(
            'Telegraph\Exception',
            'The middleware queue is empty.'
        );
        $dispatcher->handle(ServerRequestFactory::fromGlobals());
    }

    public function testInvalidMiddleware()
    {
        $queue = [
            new InvalidMiddleware(),
            $this->responseFactory()
        ];

        $resolver = new FakeResolver();
        $dispatcher = new Dispatcher($queue, $resolver);
        $this->expectException(
            'Telegraph\Exception',
            'Middleware must implement Psr\Http\Server\MiddlewareInterface'
        );
        $dispatcher->handle(ServerRequestFactory::fromGlobals());
    }

    public function testNonResponse()
    {
        $queue = [
            new class implements MiddlewareInterface {
                public function process(
                    ServerRequestInterface $request,
                    RequestHandlerInterface $handler
                ) : ResponseInterface {
                    return 'not a response object';
                }
            }
        ];

        $dispatcher = new Dispatcher($queue);
        $this->expectException('TypeError');
        $dispatcher->handle(ServerRequestFactory::fromGlobals());
    }
}
