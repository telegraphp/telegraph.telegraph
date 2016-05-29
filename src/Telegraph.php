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
 * A multiple-use PSR-7 middleware dispatcher.
 *
 * @package telegraph/telegraph
 *
 */
class Telegraph
{
    /**
     *
     * A factory to create Dispatcher objects.
     *
     * @var DispatcherFactory
     *
     */
    protected $dispatcherFactory;

    /**
     *
     * Constructor.
     *
     * @param DispatcherFactory $dispatcherFactory A factory to create
      * Dispatcher objects.
     *
     */
    public function __construct(DispatcherFactory $dispatcherFactory)
    {
        $this->dispatcherFactory = $dispatcherFactory;
    }

    /**
     *
     * Dispatches a Request through a new Dispatcher.
     *
     * @param RequestInterface $request The request.
     *
     * @return ResponseInterface
     *
     */
    public function dispatch(RequestInterface $request)
    {
        $dispatcher = $this->dispatcherFactory->newInstance();
        return $dispatcher->dispatch($request);
    }
}
