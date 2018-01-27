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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 *
 * A single-use PSR-7 middleware dispatcher.
 *
 * @package telegraph/telegraph
 *
 */
class Dispatcher implements RequestHandlerInterface
{
    /**
     *
     * The middleware queue.
     *
     * @var (MiddlewareInterface)[]
     *
     */
    protected $queue = [];

    /**
     *
     * A callable to convert queue entries to callables.
     *
     * @var callable|ResolverInterface
     *
     */
    protected $resolver;

    /**
     *
     * Constructor.
     *
     * @param (MiddlewareInterface)[] $queue The middleware queue.
     *
     * @param callable|ResolverInterface $resolver Converts queue entries to
     * callables.
     *
     */
    public function __construct(array $queue, callable $resolver = null)
    {
        $this->queue = $queue;
        $this->resolver = $resolver;
    }

    /**
     *
     * Runs the next entry in the queue.
     *
     * @param ServerRequestInterface $request The request.
     *
     * @return ResponseInterface
     *
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $entry = array_shift($this->queue);
        $middleware = $this->resolve($entry);

        if (! $middleware instanceof MiddlewareInterface) {
            throw Exception::invalidMiddleware();
        }

        return $middleware->process($request, $this);
    }

    /**
     *
     * Converts a queue entry to a callable, using the resolver if present.
     *
     * @param mixed $entry The queue entry.
     *
     * @return MiddlewareInterface
     *
     * @throws RuntimeException when the queue is empty.
     *
     */
    protected function resolve($entry)
    {
        if (! $entry) {
            throw Exception::emptyQueue();
        }

        if (! $this->resolver) {
            return $entry;
        }

        return call_user_func($this->resolver, $entry);
    }
}
