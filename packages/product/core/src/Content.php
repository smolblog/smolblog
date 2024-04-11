<?php

namespace Smolblog\Core;

use Smolblog\Core\Content\ContentExtension;
use Smolblog\Core\Content\ContentType;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\{ArrayType, Entity, EntityKit, SerializableValue, SerializableValueKit};
use Smolblog\Foundation\Value\Fields\{DateTimeField, Identifier, DateIdentifier};

/**
 * A unit of Content for the system.
 *
 * This can be almost anything. The required fields are kept intentionally sparse, with additional information reserved
 * for Extensions.
 *
 * 1) Content types can store and handle their data as they see fit. It just needs to be serializable.
 * 2) Content extensions can attach extra data to the content through the attachExtension function.
 */
readonly class Content extends Value implements SerializableValue, Entity {
	use SerializableValueKit;
	use EntityKit;

	public function __construct(
		public ContentType $body,
		public Identifier $siteId,
		public Identifier $authorId,
		?Identifier $id = null,
		public ?string $path = null,
		public ?DateTimeField $publishTimestamp = null,
		public bool $published = false,
		#[ArrayType(ContentExtension::class)] public array $extensions = [],
	) {
		$this->id = $id ?? new DateIdentifier();

		if ($this->published) {
			$this->publishTimestamp ??= new DateTimeField();
		}
	}

	public function title(): string {
		return $this->body->getTitle();
	}

	public function type(): string {
		return $this->body->getTypeKey();
	}
}
