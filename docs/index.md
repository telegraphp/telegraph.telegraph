# Middleware Signature

A _Telegraph_ middleware callable must have the following signature:

```php
use Psr\Http\Message\RequestInterface;

function (
    RequestInterface $request, // the HTTP request
    callable $next    // a dispatcher to the next middleware
) {
    // ...
}
```

A _Telegraph_ middleware callable must return an implementation of
_Psr\Http\Message\ResponseInterface_.

This signature makes _Telegraph_ appropriate for both server-related and client-
related use cases. That is, it can receive an incoming _ServerRequestInterface_
and generate an outgoing _ResponseInterface_ (acting as a server), or it can
build an outgoing _RequestInterface_ and return the resulting
_ResponseInterface_ (acting as a client).

> N.b.: Psr\Http\Message\ServerRequestInterface extends RequestInterface, so
> typehinting to RequestInterface covers both use cases.

# Middleware Dispatching

Create a `$queue` array of middleware callables:

```php
$queue[] = function (RequestInterface $request, callable $next) {
    // 1st middleware
};

$queue[] = function (RequestInterface $request, callable $next) {
    // 2nd middleware
};

// ...

$queue[] = function (RequestInterface $request, callable $next) {
    // Nth middleware
};
```

Use the _TelegraphFactory_ to create a _Telegraph_ object with the `$queue`,
and dispatch a request through the _Telegraph_ object to get back a response.

```php
/**
 * @var \Psr\Http\Message\RequestInterface $request
 * @var \Psr\Http\Message\ResponseInterface $response
 */

use Telegraph\TelegraphFactory;

$telegraphFactory = new TelegraphFactory();
$telegraph = $telegraphFactory->newInstance($queue);
$response = $telegraph->dispatch($request);
```

That will execute each of the middlewares in first-in-first-out order.

# Middleware Logic

Your middleware logic should follow this pattern:

- Receive the request object from the previous middleware as a parameter, along
  with a dispatcher to the `$next` middleware;

- Optionally modify the received request;

- Then either ...

    - Create a response and return it to the previous middleware.

- ... or:

    - Dispatch the request through the `$next` middleware, receiving a response
      in return;

    - Optionally modify the returned response;

    - Return the response to the previous middleware.

Here is a skeleton example; your own middleware may or may not perform the
various optional processes:

```php
use Psr\Http\Message\RequestInterface;

$queue[] = function (RequestInterface $request, callable $next) {

    // optionally modify the incoming Request
    $request = $request->...;

    // dispatch the $next middleware and get back a Response
    $response = $next($request);

    // optionally modify the Response if desired
    $response = $response->...;

    // return the Response to the previous middleware
    return $response;
};
```

> N.b.: You MUST return the response from your middleware logic, whether by
> creating it or by using the one returned from the `$next` middleware.

As a reminder, remember that the request is **immutable**. Implicit in that is
the fact that changes to the request are always transmitted to the `$next`
middleware but never to the previous one.

Note also that this logic chain means the request is modified on the way "in"
through the middleware, and  the response is modified on the way "out".

For example, if the middleware queue looks like this:

```php
$queue[] = function (RequestInterface $request, callable $next) {
    // "Foo"
};

$queue[] = function (RequestInterface $request, callable $next) {
    // "Bar"
};

$queue[] = function (RequestInterface $request, callable $next) {
    // "Baz"
};
```

... the path through the middlewares will look like this:

    Foo is 1st on the way in
        Bar is 2nd on the way in
            Baz is 3rd on the way in, and 1st on the way out
        Bar is 2nd on the way out
    Foo is 3rd on the way out

You can use this dual-pass logic in clever and perhaps unintuitive ways. For
example, middleware placed at the very start may do nothing with the request
and call `$next` right away, but it is the middleware with the "real" last
opportunity to modify the response.

# Creating Responses

At some point, one of your middleware callables will need to create a response
to return to the previous middleware. Because there are different response
implementations, _Telegraph_ will not create one for you; the choice of
response implementation is up to you.

Here is an example of a response-creating middleware:

```php
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response;

$queue[] = function (RequestInterface $request, callable $next) {
    return new Response();
}
```

In general, there should be no need to call `$next` at this point; merely
return the response to start its journey back through the middleware queue. As
such, the response-creating middleware entry is probably going to the the very
last one in the queue.

# Resolvers

You may wish to use `$queue` entries other than anonymous functions or already-
instantiated objects. If so, you can pass a `$resolver` callable to the
_Telegraph_ that will convert the `$queue` entry to a callable. Thus, using a
`$resolver` allows you to pass in your own factory mechanism for `$queue`
entries.

For example, this `$resolver` will naively convert `$queue` string entries to
new class instances:

```php
$resolver = function ($class) {
    return new $class();
};
```

You can then add `$queue` entries as class names, and _Telegraph_ will use the
`$resolver` to create the objects in turn.

```php
use Telegraph\TelegraphFactory;

$queue[] = 'FooMiddleware';
$queue[] = 'BarMiddleware';
$queue[] = 'BazMiddleware';

$telegraphFactory = new TelegraphFactory($resolver);
$telegraph = $telegraphFactory->newInstance($queue);
```

As long as the classes listed in the `$queue` implement
`__invoke(RequestInterface $request, callable $next)`, then _Telegraph_ will work
correctly.

# Queue Object

Sometimes using an array for the `$queue` will not be suitable. You may wish to
use an object to build the middleware queue instead.

In these cases, you can use the _TelegraphFactory_ to create the _Telegraph_
queue from any object that eimplements _Traversable_. The _TelegraphFactory_
will then convert that queue object to an array using `iterator_to_array()`.

For example, first instantiate a _TelegraphFactory_ with an optional
`$resolver` ...

```php
use Telegraph\TelegraphFactory;

$telegraphFactory = new TelegraphFactory($resolver);
```

... then instantiate a _Telegraph_ object where the `$queue` is a _Traversable_
implementation:

```php
use Traversable;

/**
 * var Traversable $queue
 */
$telegraph = $telegraphFactory->newInstance($queue);
```

You can then dispatch through the `$telegraph` as described above.


# Reusable Telegraphs

If you wish, you can reuse the same _Telegraph_ object multiple times. The same
middleware queue will be used each time you invoke that _Telegraph_ instance.
For example, if you are making multiple client requests:

```php
/**
 * @var Psr\Http\Message\ResponseInterface $response1
 * @var Psr\Http\Message\ResponseInterface $response2
 * @var Psr\Http\Message\ResponseInterface $responseN
 */
$request1 = ...;
$response1 = $telegraph->dispatch($request1);

$request2 = ...;
$response2 = $telegraph->dispatch($request2);

// ...

$requestN = ...;
$responseN = $telegraph->dispatch($requestN);
```

If you are certain that you will never reuse the _Telegraph_ instance, you can
instantiate a single-use _Dispatcher_ to avoid some minor overhead.

```php
/**
 * @var array $queue The middleware queue.
 * @var callable|null $resolver An optional queue entry resolver.
 * @var Psr\Http\Message\RequestInterface $request The HTTP request.
 */
use Telegraph\Dispatcher;

$dispatcher = new Dispatcher($queue, $resolver);
$response = $dispatcher->dispatch($request);
```
