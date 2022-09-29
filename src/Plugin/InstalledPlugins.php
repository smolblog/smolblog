<?php

namespace Smolblog\Core\Plugin;

use Smolblog\Core\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse, SecurityLevel};

/**
 * Endpoint to show currently installed plugins and whether they are active.
 */
class InstalledPlugins implements Endpoint {
	/**
	 * Construct the endpoint with the arrays from App
	 *
	 * @param PluginPackage[] $installedPackages All packages returned by Composer.
	 * @param Plugin[]        $activePlugins     All currently-active plugins.
	 */
	public function __construct(
		private array $installedPackages,
		private array $activePlugins,
	) {
	}

	/**
	 * Configuration for this endpoint
	 *
	 * @return EndpointConfig
	 */
	public function getConfig(): EndpointConfig {
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
				fn($package) => [
					...get_object_vars($package),
					'active' => array_key_exists($package->package, $this->activePlugins),
				],
				$this->installedPackages
			)
		);
	}
}
