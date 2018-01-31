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

/**
 *
 * This interface defines the dispatcher signature required by Telegraph.
 *
 * Implementing this is completely voluntary, it's mostly useful for indicating
 * that your class is a dispatcher, and to ensure you type-hint the `__invoke()`
 * method signature correctly.
 *
 * @package telegraph/telegraph
 *
 */
interface DispatcherInterface
{
    /**
     *
     * Calls the next entry in the queue.
     *
     * @param RequestInterface $request The request.
     *
     * @return ResponseInterface
     *
     */
    public function dispatch(RequestInterface $request);
}
