<?php

namespace Smolblog\Api\Server;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to get standard information about the server.
 */
class Base extends BasicEndpoint {
	public const LICENSE = [
		'notice' => <<<EOF
			<p>Smolblog &copy; 2023 Evan Hildreth and contributors</p>

			<p>This program is free software: you can redistribute it and/or modify
			it under the terms of the GNU Affero General Public License as
			published by the Free Software Foundation, either version 3 of the
			License, or (at your option) any later version.</p>

			<p>This program is distributed in the hope that it will be useful,
			but WITHOUT ANY WARRANTY; without even the implied warranty of
			MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
			GNU Affero General Public License for more details.</p>
		EOF,
		'key' => 'AGPL-3.0-only',
		'href' => 'https://www.gnu.org/licenses/agpl-3.0.html',
		'source' => [
			'smolblog/smolblog-core' => 'https://github.com/smolblog/smolblog-core',
		],
	];

	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/',
			requiredScopes: [],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param ApiEnvironment $env API Environment info.
	 */
	public function __construct(
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return ServerInfo
	 */
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): ServerInfo {
		return new ServerInfo(
			serverVersion: '0.2.0-alpha',
			specHref: $this->env->getApiUrl('/spec'),
			license: $userId ? self::LICENSE : null,
		);
	}
}
