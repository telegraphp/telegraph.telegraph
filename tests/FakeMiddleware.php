<?php
namespace Telegraph;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class FakeMiddleware implements MiddlewareInterface
{
    public static $count = 0;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $next
    ) : ResponseInterface {
        $response = $next->handle($request);
        $response->getBody()->write(++ static::$count);
        return $response;
    }
}
