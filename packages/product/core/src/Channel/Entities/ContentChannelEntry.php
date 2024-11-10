<?php

namespace Smolblog\Core\Channel\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * The result of a successful push of content to a channel.
 *
 * If the content is accessible over the web, a URL should be provided. Not every channel would use this (RSS feeds for
 * example), so don't force it. The details field is for any channel-specific metadata, such as the ID of an external
 * post.
 */
readonly class ContentChannelEntry extends Value implements SerializableValue, Entity {
	use SerializableValueKit;

	public const NAMESPACE = '1a5eb4a2-404c-4445-941c-9b04c496ef7b';

	/**
	 * Consistently build a unique identifier out of the content and channel IDs.
	 *
	 * @param string|Identifier $contentId Content ID.
	 * @param string|Identifier $channelId Channel ID.
	 * @return Identifier ID constructed from content and channel IDs.
	 */
	public static function buildId(string|Identifier $contentId, string|Identifier $channelId): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$contentId|$channelId");
	}

	/**
	 * Construct the entry.
	 *
	 * @param Identifier $contentId ID of the content.
	 * @param Identifier $channelId ID of the channel.
	 * @param Url|null   $url       Optional URL of the content on the channel.
	 * @param array      $details   Channel-specific details.
	 */
	public function __construct(
		public Identifier $contentId,
		public Identifier $channelId,
		public ?Url $url = null,
		public array $details = [],
	) {
	}


	/**
	 * Get the constructed ID from $handler and $handlerKey
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier {
		return self::buildId(contentId: $this->contentId, channelId: $this->channelId);
	}
}
