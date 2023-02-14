<?php

namespace Smolblog\RestApiBase;

use Smolblog\Framework\Objects\Identifier;

/**
 * Base Endpoint object
 *
 * The objective here is to define a REST API endpoint in a platform-agnostic way. This may not be an abstraction that
 * works forever, but it will hopefully last long enough to abstract WordPress away...
 */
abstract class BaseEndpoint {
	/**
	 * Get the HTTP verbs this endpoint responds to. Default GET.
	 *
	 * @return Verb[]
	 */
	public static function getVerbs(): array {
		return [ Verb::GET ];
	}

	/**
	 * Get the route this endpoint should live on.
	 *
	 * - Default is the fully-qualified class name minus \Smolblog\RestApiBase.
	 * - Mark in-url parameters with square brackets: "/user/[id]/posts"
	 *
	 * @return string
	 */
	public static function getRoute(): string {
		return strtolower(str_replace('\\', '/', substr(static::class, 21)));
	}

	/**
	 * Respond to the request.
	 *
	 * @param Identifier|null $userId ID of the authenticated user; null if no logged-in user.
	 * @param array           $params Associative array of any parameters in the URL or query string.
	 * @param array           $body   Array-decoded JSON body if present.
	 * @return Response
	 */
	abstract public function run(
		?Identifier $userId = null,
		array $params = [],
		array $body = [],
	): Response;
}
