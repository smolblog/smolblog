<?php

namespace Smolblog\Infrastructure\Endpoint;

/**
 * Indicates that the given Endpoint also contains custom documentation.
 */
interface DocumentedEndpoint extends Endpoint {
	/**
	 * Get the documentation for this endpoint.
	 *
	 * @return EndpointDocumentation
	 */
	public static function getDocumentation(): EndpointDocumentation;
}
