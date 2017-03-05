<?php declare(strict_types=1);

namespace ApiClients\Middleware\TokenAuthorization;

use ApiClients\Foundation\Middleware\DefaultPriorityTrait;
use ApiClients\Foundation\Middleware\ErrorTrait;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PostTrait;
use Psr\Http\Message\RequestInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

/**
 * Middleware that adds the authorization header in the token format.
 */
class TokenAuthorizationHeaderMiddleware implements MiddlewareInterface
{
    use DefaultPriorityTrait;
    use PostTrait;
    use ErrorTrait;

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function pre(RequestInterface $request, array $options = []): CancellablePromiseInterface
    {
        if (!isset($options[self::class][Options::TOKEN])) {
            return resolve($request);
        }

        if (empty($options[self::class][Options::TOKEN])) {
            return resolve($request);
        }

        return resolve(
            $request->withAddedHeader(
                'Authorization',
                'token ' . $options[self::class][Options::TOKEN]
            )
        );
    }
}
