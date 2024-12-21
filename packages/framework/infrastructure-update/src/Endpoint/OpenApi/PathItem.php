<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Email;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

readonly class PathItem extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public ?string $summary = null,
		public ?Markdown $description = null,
		public ?Operation $get = null,
		public ?Operation $put = null,
		public ?Operation $post = null,
		public ?Operation $delete = null,
		public ?Operation $options = null,
		public ?Operation $head = null,
		public ?Operation $patch = null,
		public ?Operation $trace = null,
		#[ArrayType(Server::class)] public ?array $servers = null,
		#[ArrayType(Parameter::class)] public ?array $parameters = null,
	) {
		if (empty($servers)) {
			$this->servers = null;
		}
		if (empty($parameters)) {
			$this->parameters = null;
		}

		$this->checkForDuplicateParameters();
	}

	private function checkForDuplicateParameters(): void {
		// Check for parameter objects with the same 'name' and 'location'.
	}
}
