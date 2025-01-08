<?php

namespace Smolblog\Infrastructure\Endpoint;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Http\HttpResponse;
use Throwable;

trait ErrorResponseKit {
	/**
	 * Potential Logger service to record responses to.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $log = null;

	/**
	 * Return an HttpResponse based on the given exception.
	 *
	 * @param Throwable             $exception Exception to interpret.
	 * @param RequestInterface|null $request   Original web request.
	 * @return ResponseInterface
	 */
	public function logExceptionAndGetResponse(Throwable $exception, ?RequestInterface $request = null): ResponseInterface {
		$logLevel = LogLevel::INFO;
		$response = null;

		switch (get_class($exception)) {
			case CommandNotAuthorized::class:
				$response = new HttpResponse(
					code: 403,
					body: ['error' => 'User is not authorized to perform this action.']
				);
				break;

			case EntityNotFound::class:
				$response = new HttpResponse(code: 404, body: ['error' => $exception->getMessage()]);
				break;

			case InvalidValueProperties::class:
				$response = new HttpResponse(code: 400, body: ['error' => $exception->getMessage()]);
				break;

			case CodePathNotSupported::class:
				$logLevel = LogLevel::ERROR;
				$response = new HttpResponse(
					code: 500,
					body: ['error' => 'There is an error in the server code; check the server logs for details.'],
				);
				break;

			default:
			$logLevel = LogLevel::ERROR;
				$response = new HttpResponse(
					code: 500,
					body: ['error' => 'An error occurred; check the server logs for details.'],
				);
				break;
		}//end switch

		$this->log?->log(
			level: $logLevel,
			message: 'Exception occurred in an endpoint.',
			context: [
					'error' => $exception->getMessage(),
					'file' => $exception->getFile(),
					'line' => $exception->getLine(),
					'trace' => $exception->getTraceAsString(),
					'previous' => $exception->getPrevious()?->getMessage(),
					'request' => isset($request) ? [
						'method' => $request->getMethod(),
						'path' => $request->getUri()->getPath(),
						'body' => $request->getBody()->__toString(),
					] : null,
				]
		);

		return $response;
	}
}
