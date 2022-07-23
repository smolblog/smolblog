<?php

namespace Smolblog\Core;

use Smolblog\Core\Definitions\HttpVerb;
use Smolblog\Core\Definitions\SecurityLevel;

/**
 * Class for declaring a REST API endpoint.
 */
abstract class Endpoint {
	/**
	 * Application instance this endpoint belongs to.
	 *
	 * @var App
	 */
	private App $app;

	/**
	 * The given route for this endpoint. If the endpoint is
	 * `smolblog.com/api/blog/info`, then this function should return
	 * `/blog/info` or `blog/info` (the opening slash will be inferred).
	 *
	 * To declare URL parameters, like `/blog/57/info`, use a regular expression:
	 * `/blog/(?P<id>[0-9]+)/info`.
	 *
	 * @var string
	 */
	public readonly string $route;

	/**
	 * HTTP verbs this endpoint will respond to. Given as an array of HttpVerb
	 * enum instances.
	 *
	 * @var HttpVerb[]
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
	 */
	public readonly array $verbs;

	/**
	 * Security level for this endpoint. The user making the request will need to
	 * have permissions at or above this level or a 401 or 403 response will be
	 * given.
	 *
	 * @var SecurityLevel
	 */
	public readonly SecurityLevel $security;

	/**
	 * Parameters for this endpoint in an array of EndpointParameters.
	 *
	 * Parameters given in this array will be proviced to run()
	 *
	 * @var EndpointParameter[]
	 */
	public readonly array $params;

	/**
	 * Create the Endpoint.
	 *
	 * @param App $app Application instance.
	 */
	public function __construct(App $app) {
		$this->app = $app;

		$this->route = $this->initRoute();
		$this->verbs = $this->initVerbs();
		$this->security = $this->initSecurity();
		$this->params = $this->initParams();

		$this->setup();
	}

	/**
	 * Perform the action associated with this endpoint and return the response.
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return EndpointResponse Response to give
	 */
	abstract public function run(EndpointRequest $request): EndpointResponse;

	/**
	 * Create a route from the class' fully-qualified name.
	 *
	 * @return string generated route
	 */
	protected function getRouteFromName(): string {
		$lowercase_name = strtolower(get_class($this));
		return str_replace('\\', '/', $lowercase_name);
	}

	protected function initRoute(): string {
		return $this->getRouteFromName();
	}
	protected function initVerbs(): array {
		return [HttpVerb::GET];
	}
	protected function initSecurity(): SecurityLevel {
		return SecurityLevel::Anonymous;
	}
	protected function initParams(): array {
		return [];
	}

	/**
	 * Perform any other actions needed during initialization. Called by the
	 * superclass during `__construct` after member variables are assigned.
	 *
	 * @return void
	 */
	protected function setup(): void {
	}
}
