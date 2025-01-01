<?php

namespace Smolblog\Infrastructure\OpenApi;

interface OpenApiDocumentedValue {
	/**
	 * Get the OpenAPI documentation for this endpoint.
	 *
	 * @return OpenApiObjectSchema
	 */
	public static function getOpenApiSchema(): OpenApiObjectSchema;
}
