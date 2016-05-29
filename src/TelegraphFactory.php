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
 * A builder to create Telegraph objects.
 *
 * @package telegraph/telegraph
 *
 */
class TelegraphFactory
{
    /**
     *
     * Creates a new Telegraph with the specified queue for its Dispatcher objects.
     *
     * @param array|Traversable $queue The queue specification.
     *
     * @param callable|ResolverInterface $resolver Converts queue entries to
     * callables.
     *
     * @return Telegraph
     *
     */
    public function newInstance($queue, $resolver = null)
    {
        return new Telegraph(new DispatcherFactory($queue, $resolver));
    }
}
