<?php

namespace Smolblog\Infrastructure\Endpoint;

use Smolblog\Foundation\Value\Http\HttpVerb;
use Smolblog\Foundation\Value\Traits\ServiceConfiguration;
use Smolblog\Foundation\Value\Traits\ServiceConfigurationKit;

/**
 * Configuration information for a REST endpoint.
 */
readonly class EndpointConfiguration implements ServiceConfiguration {
	use ServiceConfigurationKit;

	/**
	 * Construct the configuration.
	 *
	 * @param string                $route         Route for the endpoint; add placeholder variables with {var}.
	 * @param string                $key           Unique name for this endpoint; FQCN recommended.
	 * @param HttpVerb              $verb          HTTP method for the endpoint, default GET.
	 * @param array<string, string> $pathVariables Keys for each placeholder in $route, value is class or RegEx.
	 * @param string|null           $scope         Required scope for the endpoint; null if no auth required.
	 */
	public function __construct(
		public string $route,
		string $key,
		public HttpVerb $verb = HttpVerb::GET,
		public array $pathVariables = [],
		public ?string $scope = null,
	) {
		$this->key = $key;
	}
}
