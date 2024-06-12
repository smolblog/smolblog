<?php

namespace Smolblog\WP\Helpers;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Foundation\Value\Fields\Identifier;

class DebugEndpoint extends BasicEndpoint {
	public static function getConfiguration(): EndpointConfig
	{
		return new EndpointConfig(
			route: '/debug',
			requiredScopes: [],
		);
	}

	public function __construct(
		private array $depMap,
		private ConnectionInterface $db,
	) {
	}

	public function run(?Identifier $userId, ?array $params, ?object $body): GenericResponse
	{
		$map = $this->depMap;
		unset($map[self::class]);
		return new GenericResponse(
			map: array_map(
				fn($da) => is_array($da) ? array_map(
					fn($di) => is_callable($di) ? $di() : $di,
					$da
				) : $da,
				$map
			),
			database: print_r($this->db, true),
			rewrite: (new \WP_Rewrite())->wp_rewrite_rules(),
		);
	}
}
