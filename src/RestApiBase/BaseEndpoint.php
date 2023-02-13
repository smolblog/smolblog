<?php

namespace Smolblog\RestApiBase;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

abstract class BaseEndpoint {
	public static function getVerbs(): array {
		return [ Verb::GET ];
	}

	public static function getRoute(): string {
		return strtolower(str_replace('\\', '/', substr(static::class, 21)));
	}

	public static function isAuthorized(): bool {
		return false;
	}

	abstract public function run(
		?Identifier $userId = null,
		array $params = [],
		array $body = [],
	): Response;

	protected function successResponse(Value $body) {
		return new Response(body: $body);
	}

	protected function errorResponse(Value $body) {
		return new Response(body: $body, status: 500);
	}
}
