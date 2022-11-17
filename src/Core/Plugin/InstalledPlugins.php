<?php

namespace Smolblog\Core\Plugin;

use Smolblog\App\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse, SecurityLevel};

/**
 * Endpoint to show currently installed plugins and whether they are active.
 */
class InstalledPlugins implements Endpoint {
	/**
	 * Construct the endpoint with the arrays from App
	 *
	 * @param string[] $installedPlugins All plugins loaded into the App.
	 */
	public function __construct(
		private array $installedPlugins,
	) {
	}

	/**
	 * Configuration for this endpoint
	 *
	 * @return EndpointConfig
	 */
	public static function config(): EndpointConfig {
		return new EndpointConfig(
			route: 'admin/plugins',
			security: SecurityLevel::Admin
		);
	}

	/**
	 * Perform the action associated with this endpoint and return the response.
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return EndpointResponse Response to give
	 */
	public function run(EndpointRequest $request): EndpointResponse {
		return new EndpointResponse(
			statusCode: 200,
			body: array_map(
				fn($pluginClass) => $pluginClass::config(),
				$this->installedPlugins
			)
		);
	}
}
