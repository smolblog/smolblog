<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 *
 */
readonly class Operation extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public string $operationId,
		#[ArrayType(ArrayType::TYPE_STRING)] public array $tags = [],
		public ?string $summary = null,
		public ?Markdown $description = null,
		#[ArrayType(Parameter::class)] public ?array $parameters = null,
		public ?RequestBody $requestBody = null,
		#[ArrayType(Response::class, isMap: true)] public ?array $responses = null,
		public bool $deprecated = false,
		#[ArrayType(SecurityRequirement::class, isMap: true)] public ?array $security = null,
		#[ArrayType(Server::class)] public ?array $servers = null,
	) {
	}
}
