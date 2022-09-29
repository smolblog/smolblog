<?php

namespace Smolblog\Core\Endpoint;

/**
 * Interface for a class that can take a Smolblog\Core\Endpoint and register it correctly with the external system.
 */
interface EndpointRegistrar {
	/**
	 * Register the given endpoint with the REST API
	 *
	 * @param Endpoint $endpoint Endpoint to register.
	 * @return void
	 */
	public function registerEndpoint(Endpoint $endpoint): void;
}
