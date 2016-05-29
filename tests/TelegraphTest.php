<?php
namespace Telegraph;

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class TelegraphTest extends \PHPUnit_Framework_TestCase
{
    public function test()
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

        $builder = new TelegraphFactory();
        $telegraph = $builder->newInstance($queue);

        // dispatch once
        $response = $telegraph->dispatch(ServerRequestFactory::fromGlobals());
        $actual = (string) $response->getBody();
        $this->assertSame('123', $actual);

        // dispatch again
        $response = $telegraph->dispatch(ServerRequestFactory::fromGlobals());
        $actual = (string) $response->getBody();
        $this->assertSame('456', $actual);
    }
}
