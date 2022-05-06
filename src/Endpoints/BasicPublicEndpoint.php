<?php

namespace Smolblog\Core\Endpoints;

use JsonSerializable;
use Smolblog\Core\Definitions\Endpoint;
use Smolblog\Core\Definitions\HttpVerb;
use Smolblog\Core\Definitions\SecurityLevel;
use Smolblog\Core\Definitions\EndpointParameter;
use Smolblog\Core\Definitions\EndpointRequest;
use Smolblog\Core\Definitions\HttpResponse;

/**
 * An abastract Endpoint class that takes care of most of the basics. Provides
 * some sensible defaults for getting an Endpoint built quickly. More advanced
 * classes will probably want to implement the Endpoint interface directly and
 * not inherit from this class.
 */
abstract class BasicPublicEndpoint implements Endpoint {
	/**
	 * Generates a route for the endpoint based on the class' fully qualified
	 * name.
	 *
	 * @return string Route for this Endpoint.
	 */
	public function route(): string {
		$lowercase_name = strtolower(get_class($this));
		return str_replace('\\', '/', $lowercase_name);
	}

	/**
	 * HTTP verbs this endpoint will respond to. Given as an array of HttpVerb
	 * enum instances.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
	 *
	 * @return HttpVerb[]
	 */
	public function verbs(): array {
		return [ HttpVerb::GET ];
	}

	/**
	 * Security level for this endpoint. The user making the request will need to
	 * have permissions at or above this level or a 401 or 403 response will be
	 * given.
	 *
	 * @return SecurityLevel
	 */
	public function security(): SecurityLevel {
		return SecurityLevel::Anonymous;
	}

	/**
	 * Parameters for this endpoint in an array of EndpointParameters.
	 *
	 * Parameters given in this array will be proviced to run()
	 *
	 * @return EndpointParameter[]
	 */
	public function params(): array {
		return [];
	}

	/**
	 * Returns a 200 HttpResponse with the JSON-encoded result of responseBody().
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return HttpResponse Response to give
	 */
	public function run(EndpointRequest $request): HttpResponse {
		$body = json_encode($this->responseBody());

		return new class ($body) implements HttpResponse {
			/**
			 * Store the body response
			 *
			 * @var string
			 */
			private string $body;

			/**
			 * Store the given body in the instance
			 *
			 * @param string $body Body of the response.
			 */
			public function __construct(string $body) {
				$this->body = $body;
			}

			/**
			 * Successful response
			 *
			 * @return integer
			 */
			public function statusCode(): int {
				return 200;
			}

			/**
			 * No special headers
			 *
			 * @return array
			 */
			public function headers(): array {
				return [];
			}

			/**
			 * The given body
			 *
			 * @return string
			 */
			public function body(): string {
				return $this->body;
			}
		};
	}

	/**
	 * Hook function for the child class to supply the response. Should be
	 * provided unserialized; parent class will serialize and package.
	 *
	 * @return array|JsonSerializable Unserialized body of the response.
	 */
	abstract protected function responseBody(): array|JsonSerializable;
}
