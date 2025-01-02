<?php

namespace Smolblog\Infrastructure\Endpoint;

use Smolblog\Foundation\Value\Http\HttpVerb;
use Smolblog\Foundation\Value\Traits\ArrayType;
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
	 * @param boolean               $auth          True if this endpoint requires authentication.
	 */
	public function __construct(
		public string $route,
		public HttpVerb $verb = HttpVerb::GET,
		#[ArrayType(ArrayType::TYPE_STRING, isMap: true)] public array $pathVariables = [],
		public bool $auth = true,
	) {
	}
}
