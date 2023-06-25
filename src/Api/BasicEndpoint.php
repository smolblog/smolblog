<?php

namespace Smolblog\Api;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Api\Exceptions\ErrorResponse;
use Smolblog\Framework\Exceptions\MessageNotAuthorizedException;
use Smolblog\Framework\Objects\HttpResponse;
use Smolblog\Framework\Objects\Identifier;
use Throwable;

/**
 * Handle endpoints that consist of JSON bodies or basic parameters.
 */
abstract class BasicEndpoint implements Endpoint {
	/**
	 * Respond to the request.
	 *
	 * @param Identifier|null $userId ID of the authenticated user; null if no logged-in user.
	 * @param array|null      $params Associative array of any parameters in the URL or query string.
	 * @param object|null     $body   JSON body if present.
	 * @return mixed
	 */
	abstract public function run(
		?Identifier $userId,
		?array $params,
		?object $body,
	): mixed;

	/**
	 * Translate the standard Request to the given run method.
	 *
	 * @param ServerRequestInterface $request Incoming request.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		$config = static::getConfiguration();

		$userId = $request->getAttribute('smolblogUserId');
		$requestParams = array_merge($request->getQueryParams(), $request->getAttribute('smolblogPathVars', []));

		try {
			$body = null;
			if (class_exists($config->bodyClass)) {
				$body = $config->bodyClass::jsonDeserialize($request->getBody()->getContents());
			}

			$params = [];
			foreach ($requestParams as $key => $val) {
				$type = $config->pathVariables[$key] ?? $config->queryVariables[$key] ?? null;
				if (!isset($type)) {
					continue;
				}

				if ($type->type == 'string' && $type->format == 'uuid') {
					$params[$key] = Identifier::fromString($val);
					continue;
				}
				if ($type->type == 'boolean') {
					$params[$key] = $val ? true : false;
					continue;
				}

				$params[$key] = $val;
			}//end foreach

			$result = $this->run(
				userId: $userId,
				params: $params,
				body: $body,
			);

			switch (get_class($result)) {
				case SuccessResponse::class:
					return new HttpResponse(code: 204);

				case RedirectResponse::class:
					return new HttpResponse(
						code: $result->permanent ? 301 : 302,
						headers: ['Location' => $result->url],
					);

				default:
					return new HttpResponse(body: $result);
			}
		} catch (ErrorResponse $ex) {
			return new HttpResponse(code: $ex->getHttpCode(), body: $ex);
		} catch (MessageNotAuthorizedException $ex) {
			return new HttpResponse(code: 403, body: $ex);
		} catch (Throwable $ex) {
			return new HttpResponse(
				code: 500,
				body: [
					'code' => 500,
					'error' => $ex->getMessage(),
					'debug' => [
						'user' => $userId->toString(),
						'params' => $requestParams,
						'body' => $request->getBody()->getContents(),
					],
					'file' => $ex->getFile(),
					'line' => $ex->getLine(),
					'trace' => $ex->getTraceAsString(),
					'previous' => $ex->getPrevious()?->getMessage()
				]
			);
		}//end try
	}
}
