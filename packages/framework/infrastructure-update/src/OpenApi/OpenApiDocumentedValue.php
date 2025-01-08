<?php

namespace Smolblog\Infrastructure\OpenApi;

/**
 * A Value object that provides its own OpenAPI documentation.
 *
 * Unlike Endpoints, most Value objects can have their schema inferred by their structure. In the case of the outliers,
 * this interface provides a way to break out of the defaults.
 */
interface OpenApiDocumentedValue {
	/**
	 * Get the OpenAPI documentation for this endpoint.
	 *
	 * @return OpenApiObjectSchema
	 */
	public static function getOpenApiSchema(): OpenApiObjectSchema;
}
