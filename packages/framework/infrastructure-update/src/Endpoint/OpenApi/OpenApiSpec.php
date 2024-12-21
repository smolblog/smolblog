<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Root object describing an OpenAPI spec.
 *
 * @see https://learn.openapis.org/specification/
 */
readonly class OpenApiSpec extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public string $openapi,
		public Info $info,
		#[ArrayType(Server::class)] public array $servers = [],
		#[ArrayType(PathItem::class, isMap: true)] public ?array $paths = null,
		public ?object $components = null,
		public ?object $security = null,
		#[ArrayType(ArrayType::TYPE_ARRAY)] public ?array $tags = null,
	) {
		if (empty($servers)) {
			$this->servers = [
				new Server(url: new Url('/')),
			];
		}
	}
}
