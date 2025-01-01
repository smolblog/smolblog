<?php

namespace Smolblog\Infrastructure\OpenApi;

use Smolblog\Infrastructure\Endpoint\Endpoint;

/**
 * An endpoint that provides its own OpenAPI documentation.
 *
 * This interface is intentionally vague and specific. Currently, documentation is only auto-generated in OpenAPI
 * format, so the method signature reflects that. Beyond that, leeway is given for the implementing class to provide
 * the info however it sees fit. Traits and superclasses are available that will auto-generate from provided
 * information, but manually providing the documentation is also an option.
 */
interface OpenApiDocumentedEndpoint extends Endpoint {
	/**
	 * Get the OpenAPI documentation for this endpoint.
	 *
	 * @return OpenApiEndpointSpec
	 */
	public static function getOpenApiSpec(): OpenApiEndpointSpec;
}
