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

use Traversable;

/**
 *
 * A factory to create (and re-create) Dispatcher objects.
 *
 * @package telegraph/telegraph
 *
 */
class DispatcherFactory
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
     * A callable to convert queue entries to callables or implementations of
     * MiddlewareInterface.
     *
     * @var callable|ResolverInterface
     *
     */
    protected $resolver;

    /**
     *
     * Constructor.
     *
     * @param array|Traversable $queue The middleware queue.
     *
     * @param callable|ResolverInterface $resolver Converts queue entries to Middleware
     *
     */
    public function __construct($queue, $resolver = null)
    {
        if ($queue instanceof Traversable) {
            $queue = iterator_to_array($queue);
        }

        if (! is_array($queue)) {
            throw Exception::invalidQueue();
        }

        $this->queue = $queue;
        $this->resolver = $resolver;
    }

    /**
     *
     * Returns a new Dispatcher.
     *
     * @return Dispatcher
     *
     */
    public function newInstance()
    {
        return new Dispatcher($this->queue, $this->resolver);
    }
}
