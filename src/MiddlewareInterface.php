<?php
/**
 *
 * This file is part of Telegraph for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @copyright 2016, Paul M. Jones
 *
 */
namespace Telegraph;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * This interface defines the middleware interface signature required by Telegraph.
 *
 * Implementing this is completely voluntary, it's mostly useful for indicating
 * that your class is middleware, and to ensure you type-hint the `__invoke()`
 * method signature correctly.
 *
 * @package telegraph/telegraph
 *
 */
interface MiddlewareInterface
{
    /**
     *
     * Middleware logic to be invoked.
     *
     * @param Request $request The request.
     *
     * @param callable|DispatcherInterface $next The middleware dispatcher.
     *
     * @return ResponseInterface
     *
     */
    public function __invoke(RequestInterface $request, callable $next);
}
