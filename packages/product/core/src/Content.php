<?php

namespace Smolblog\Core;

use Smolblog\Core\Content\Extension\ContentExtension;
use Smolblog\Core\Content\Type\ContentType;
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
 * 2) Content extensions can attach extra data to the content.
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
	}

	public function title(): string {
		return $this->body->getTitle();
	}

	public function type(): string {
		return get_class($this->body)::KEY;
	}

	/**
	 * Find out if this content is publicly available somewhere.
	 *
	 * Currently checks $this->published, but could change in the future.
	 *
	 * @return boolean
	 */
	public function isPublic(): bool {
		return $this->published;
	}
}
