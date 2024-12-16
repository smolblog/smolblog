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
	 * @param HttpVerb              $verb          HTTP method for the endpoint, default GET.
	 * @param array<string, string> $pathVariables Keys for each placeholder in $route, value is class or RegEx.
	 * @param AuthScope|null        $scope         Required scope for the endpoint; null if no auth required.
	 */
	public function __construct(
		public string $route,
		public HttpVerb $verb = HttpVerb::GET,
		public array $pathVariables = [],
		public ?AuthScope $scope = null,
	) {
	}
}
