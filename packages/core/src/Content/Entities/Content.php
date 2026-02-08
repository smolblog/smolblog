<?php

namespace Smolblog\Core\Content\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use DateTimeInterface;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;

/**
 * A unit of Content for the system.
 *
 * This can be almost anything. The required fields are kept intentionally sparse, with additional information reserved
 * for Extensions.
 *
 * 1) Content types can store and handle their data as they see fit. It just needs to be serializable.
 * 2) Content extensions can attach extra data to the content.
 *
 * @template T of ContentType
 */
readonly class Content implements Value, Entity {
	use ValueKit;

	/**
	 * Create the Content.
	 *
	 * @param T                      $body             The ContentType for this content.
	 * @param UuidInterface          $siteId           ID for the Site this belongs to.
	 * @param UuidInterface          $userId           ID of the user responsible for this content.
	 * @param UuidInterface          $id               ID for the content.
	 * @param DateTimeInterface|null $publishTimestamp Time and date the content was first published.
	 * @param Url|null               $canonicalUrl     Canonical absolute URL to the content if it exists.
	 * @param ContentExtension[]     $extensions       Data for any extensions attached to this content.
	 * @param ContentChannelEntry[]  $links            Channels this Content has been pushed to with relevant details.
	 */
	public function __construct(
		public ContentType $body,
		public UuidInterface $siteId,
		public UuidInterface $userId,
		public UuidInterface $id,
		public ?DateTimeInterface $publishTimestamp = null,
		public ?UriInterface $canonicalUrl = null,
		#[ListType(ContentExtension::class)] public array $extensions = [],
		#[ListType(ContentChannelEntry::class)] public array $links = [],
	) {}

	/**
	 * Title for this content.
	 *
	 * @return string
	 */
	public function title(): string {
		return $this->body->title;
	}

	/**
	 * Type of this content.
	 *
	 * @return string
	 */
	public function type(): string {
		return get_class($this->body)::getKey();
	}
}
