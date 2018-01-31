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

/**
 *
 * Package-level exception with static factories.
 *
 * @package telegraph/telegraph
 *
 */
class Exception extends \Exception
{
    /**
     *
     * The middleware queue is of an invalid type.
     *
     * @return Exception
     *
     */
    static public function invalidQueue()
    {
        return new Exception(
            'The middleware queue must be an array or a Traversable.'
        );
    }

    /**
     *
     * The middleware is invalid.
     *
     * @return Exception
     *
     */
    static public function invalidMiddleware()
    {
        return new Exception(
            'Middleware must implement Psr\Http\Server\MiddlewareInterface'
        );
    }

    /**
     *
     * The middleware queue is empty.
     *
     * @return Exception
     *
     */
    static public function emptyQueue()
    {
        return new Exception('The middleware queue is empty.');
    }

}
