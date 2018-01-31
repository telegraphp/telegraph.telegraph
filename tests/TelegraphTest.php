<?php
namespace Telegraph;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class TelegraphTest extends TestCase
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

    public function test()
    {
        FakeMiddleware::$count = 0;

        $queue = [
            new FakeMiddleware(),
            new FakeMiddleware(),
            new FakeMiddleware(),
            $this->responseFactory()
        ];

        $builder = new TelegraphFactory();
        $telegraph = $builder->newInstance($queue);

        // dispatch once
        $response = $telegraph->handle(ServerRequestFactory::fromGlobals());
        $actual = (string) $response->getBody();
        $this->assertSame('123', $actual);

        // dispatch again
        $response = $telegraph->handle(ServerRequestFactory::fromGlobals());
        $actual = (string) $response->getBody();
        $this->assertSame('456', $actual);
    }
}
