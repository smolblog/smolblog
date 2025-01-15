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
	/**
	 * Construct the configuration.
	 *
	 * @param string                $route         Route for the endpoint; add placeholder variables with {var}.
	 * @param HttpVerb              $verb          HTTP method for the endpoint, default GET.
	 * @param array<string, string> $pathVariables Keys for each placeholder in $route, value is class or RegEx.
	 * @param boolean               $auth          True if this endpoint requires authentication.
	 * @param string|null           $key           Optional unique key for this endpoint; default uses $route and $verb.
	 */
	public function __construct(
		public string $route,
		public HttpVerb $verb = HttpVerb::GET,
		#[ArrayType(ArrayType::TYPE_STRING, isMap: true)] public array $pathVariables = [],
		public bool $auth = true,
		private ?string $key = null,
	) {
	}

	/**
	 * Create the Key from $route and $verb.
	 *
	 * @return string
	 */
	public function getKey(): string {
		return $this->key ?? "{$this->verb->value} {$this->route}";
	}
}
