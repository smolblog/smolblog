<?php

namespace Smolblog\Core;

/**
 * Interface for defining and running code for a REST API endpoint.
 */
interface Endpoint {
	/**
	 * Get the configuration needed to register this Endpoint with the outside router.
	 *
	 * @return EndpointConfig
	 */
	public function getConfig(): EndpointConfig;

	/**
	 * Perform the action associated with this endpoint and return the response.
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return EndpointResponse Response to give
	 */
	public function run(EndpointRequest $request): EndpointResponse;
}
