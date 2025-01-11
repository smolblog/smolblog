<?php

namespace Smolblog\Infrastructure\Endpoint\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Http\HttpResponse;
use Throwable;

/**
 * PSR-15 middleware to handle framework-specific exceptions.
 *
 * Use this middleware to map framework exceptions to standard responses. This can be used to move error code out of
 * individual endpoints. Exceptions are handled in the following ways:
 *
 * - CommandNotAuthorized: Log notice, return HTTP 403 with given exception message.
 * - EntityNotFound: Log info, return HTTP 404 with given exception message.
 * - InvalidValueProperties: Log info, return HTTP 400 with given exception message.
 * - CodePathNotSupported: Log error, return HTTP 500 with generic message.
 *
 * Other exceptions and errors are not caught by this middleware.
 */
class ErrorResponseMiddleware implements MiddlewareInterface {
	/**
	 * Create the middleware.
	 *
	 * @param LoggerInterface|null $log Optional PSR-4 logger.
	 */
	public function __construct(private ?LoggerInterface $log = null) {
	}

	/**
	 * Pass the request to the handler and catch any framework exceptions.
	 *
	 * @param ServerRequestInterface  $request Incoming request.
	 * @param RequestHandlerInterface $handler Next RequestHandler.
	 * @return ResponseInterface
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		try {
			return $handler->handle($request);
		} catch (CommandNotAuthorized $e) {
			$this->log?->notice(
				message: "Unauthorized request to {$request->getMethod()} {$request->getUri()->getPath()}",
				context: $this->logContext($e, $request),
			);
			return new HttpResponse(code: 403, body: ['error' => $e->getMessage()]);
		} catch (EntityNotFound $e) {
			$this->log?->info(
				message: "No result at {$request->getMethod()} {$request->getUri()->getPath()}",
				context: $this->logContext($e, $request),
			);
			return new HttpResponse(code: 404, body: ['error' => $e->getMessage()]);
		} catch (InvalidValueProperties $e) {
			$this->log?->info(
				message: "Bad request to {$request->getMethod()} {$request->getUri()->getPath()}",
				context: $this->logContext($e, $request),
			);
			return new HttpResponse(code: 400, body: ['error' => $e->getMessage()]);
		} catch (CodePathNotSupported $e) {
			$this->log?->error(
				message: "Coding error in system.",
				context: $this->logContext($e, $request),
			);
			return new HttpResponse(
				code: 500,
				body: ['error' => 'There is an error in the server code; check the logs for details.']
			);
		}//end try
	}

	/**
	 * Create a general log context from an exception and request.
	 *
	 * @param Throwable        $exception Exception being handled.
	 * @param RequestInterface $request   Request being handled.
	 * @return array
	 */
	private function logContext(Throwable $exception, RequestInterface $request): array {
		return [
			'error' => $exception->getMessage(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'trace' => $exception->getTraceAsString(),
			'previous' => $exception->getPrevious()?->getMessage(),
			'request' => isset($request) ? [
				'method' => $request->getMethod(),
				'path' => $request->getUri()->getPath(),
				'headers' => $request->getHeaders(),
				'body' => $request->getBody()->__toString(),
			] : null,
		];
	}
}
