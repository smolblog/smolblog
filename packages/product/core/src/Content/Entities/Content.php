<?php

namespace Smolblog\Core\Content\Entities;

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\{ArrayType, Entity, EntityKit, SerializableValue, SerializableValueKit};
use Smolblog\Foundation\Value\Fields\{DateTimeField, Identifier, DateIdentifier, Url};

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

	/**
	 * Create the Content.
	 *
	 * @param ContentType           $body             The ContentType for this content.
	 * @param Identifier            $siteId           ID for the Site this belongs to.
	 * @param Identifier            $userId           ID of the user responsible for this content.
	 * @param Identifier|null       $id               ID for the content; will be generated if not given.
	 * @param DateTimeField|null    $publishTimestamp Time and date the content was first published.
	 * @param Url|null              $canonicalUrl     Canonical absolute URL to the content if it exists.
	 * @param ContentExtension[]    $extensions       Data for any extensions attached to this content.
	 * @param ContentChannelEntry[] $links            Channels this Content has been pushed to with relevant details.
	 */
	public function __construct(
		public ContentType $body,
		public Identifier $siteId,
		public Identifier $userId,
		?Identifier $id = null,
		public ?DateTimeField $publishTimestamp = null,
		public ?Url $canonicalUrl = null,
		#[ArrayType(ContentExtension::class)] public array $extensions = [],
		#[ArrayType(ContentChannelEntry::class)] public array $links = [],
	) {
		$this->id = $id ?? new DateIdentifier();
	}

	/**
	 * Title for this content.
	 *
	 * @return string
	 */
	public function title(): string {
		return $this->body->getTitle();
	}

	/**
	 * Type of this content.
	 *
	 * @return string
	 */
	public function type(): string {
		return get_class($this->body)::KEY;
	}
}
