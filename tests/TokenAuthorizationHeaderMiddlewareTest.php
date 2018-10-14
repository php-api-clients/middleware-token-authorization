<?php declare(strict_types=1);

namespace ApiClients\Tests\Middleware\TokenAuthorization;

use ApiClients\Middleware\TokenAuthorization\Options;
use ApiClients\Middleware\TokenAuthorization\TokenAuthorizationHeaderMiddleware;
use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use RingCentral\Psr7\Request;
use function Clue\React\Block\await;

final class TokenAuthorizationHeaderMiddlewareTest extends TestCase
{
    public function preProvider()
    {
        yield [
            [],
            false,
            '',
        ];

        yield [
            [
                TokenAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => '',
                ],
            ],
            false,
            '',
        ];

        yield [
            [
                TokenAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => null,
                ],
            ],
            false,
            '',
        ];

        yield [
            [
                TokenAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => 'kroket',
                ],
            ],
            true,
            'token kroket',
        ];
    }

    /**
     * @dataProvider preProvider
     */
    public function testPre(array $options, bool $hasHeader, string $expectedHeader)
    {
        $request = new Request('GET', 'https://example.com/');
        $middleware = new TokenAuthorizationHeaderMiddleware();
        $changedRequest = await($middleware->pre($request, 'abc', $options), Factory::create());

        if ($hasHeader === false) {
            self::assertFalse($changedRequest->hasHeader('Authorization'));

            return;
        }

        self::assertTrue($changedRequest->hasHeader('Authorization'));
        self::assertSame($expectedHeader, $changedRequest->getHeaderLine('Authorization'));
    }
}
