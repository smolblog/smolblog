<?php

namespace Smolblog\Core\Channel\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * Represents a single content channel, such as a blog, RSS feed, or social media profile. Since some social media
 * providers allow multiple profiles/blogs/channels/etc. per account, this is its own Entity.
 */
readonly abstract class Channel extends Value implements Entity, SerializableValue {
	use SerializableSupertypeKit;

	public const NAMESPACE = '144af6d4-b4fb-4500-bb28-8e729cc7f585';

	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string $handler    Key for the handler this is tied to (usually provider name).
	 * @param string $handlerKey Unique identifier for this account at this provider.
	 * @return Identifier ID constructed from provider and key.
	 */
	public static function buildId(string $handler, string $handlerKey): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$handler|$handlerKey");
	}

	/**
	 * Fall back to BasicChannel on deserialization.
	 *
	 * @return string
	 */
	private static function getFallbackClass(): string {
		return BasicChannel::class;
	}

	/**
	 * Construct the Channel
	 *
	 * @param string          $handler      Key for the handler this is tied to (usually provider name).
	 * @param string          $handlerKey   Unique identifier for this account at this provider.
	 * @param string          $displayName  Recognizable name for the channel (URL or handle?).
	 * @param Identifier|null $userId       User responsible for this Channel (if applicable).
	 * @param Identifier|null $connectionId Connection needed to authenticate for this channel (if necessary).
	 */
	public function __construct(
		public string $handler,
		public string $handlerKey,
		public string $displayName,
		public ?Identifier $userId = null,
		public ?Identifier $connectionId = null,
	) {
	}

	/**
	 * Get the constructed ID from $handler and $handlerKey
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier {
		return self::buildId(handler: $this->handler, handlerKey: $this->handlerKey);
	}
}
