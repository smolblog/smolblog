<?php

namespace Smolblog\Core\Channel\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Crell\Serde\Attributes\Field;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * The result of a successful push of content to a channel.
 *
 * If the content is accessible over the web, a URL should be provided. Not every channel would use this (RSS feeds for
 * example), so don't force it. The details field is for any channel-specific metadata, such as the ID of an external
 * post.
 */
class ContentChannelEntry implements Value, Entity {
	use ValueKit;

	public const NAMESPACE = '1a5eb4a2-404c-4445-941c-9b04c496ef7b';

	/**
	 * Consistently build a unique identifier out of the content and channel IDs.
	 *
	 * @param string|UuidInterface $contentId Content ID.
	 * @param string|UuidInterface $channelId Channel ID.
	 * @return UuidInterface ID constructed from content and channel IDs.
	 */
	public static function buildId(string|UuidInterface $contentId, string|UuidInterface $channelId): UuidInterface {
		return UuidFactory::named(namespace: self::NAMESPACE, name: "$contentId|$channelId");
	}

	/**
	 * Construct the entry.
	 *
	 * @param UuidInterface $contentId ID of the content.
	 * @param UuidInterface $channelId ID of the channel.
	 * @param UriInterface|null   $url       Optional URL of the content on the channel.
	 * @param array      $details   Channel-specific details.
	 */
	public function __construct(
		public UuidInterface $contentId,
		public UuidInterface $channelId,
		public ?UriInterface $url = null,
		#[MapType('mixed')] public array $details = [],
	) {}


	/**
	 * Get the constructed ID from $handler and $handlerKey
	 *
	 * @var UuidInterface
	 */
	#[Field(exclude: true)]
	public UuidInterface $id {
		get => self::buildId(contentId: $this->contentId, channelId: $this->channelId);
	}
}
