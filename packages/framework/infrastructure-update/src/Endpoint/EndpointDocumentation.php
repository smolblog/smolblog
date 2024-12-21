<?php

namespace Smolblog\Infrastructure\Endpoint;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Markdown;

/**
 * Store documentation information for an Endpoint.
 *
 * This is additional information that isn't typically required by a routing system. It is used to generate a spec
 * and/or documentation for the endpoint.
 */
readonly class EndpointDocumentation extends Value {
	/**
	 * Create the object.
	 *
	 * @param string                     $oneline        Single-line description of the endpoint.
	 * @param Markdown|null              $longform       Markdown-formatted longer description of the endpoint.
	 * @param array<string, string>|null $queryVariables Keys for each accepted query variable, value is class or RegEx.
	 * @param string|array|null          $bodyShape      Class or key-value array describing the expected JSON body.
	 * @param string|array|null          $responseShape  Class or key-value array describing the expected JSON result.
	 */
	public function __construct(
		public string $oneline,
		public ?Markdown $longform = null,
		public ?array $queryVariables = null,
		public string|array|null $bodyShape = null,
		public string|array|null $responseShape = null,
	) {
	}
}
