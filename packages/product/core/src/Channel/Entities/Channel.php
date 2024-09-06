<?php

namespace Smolblog\Core\Channel\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Represents a single content channel, such as a blog, RSS feed, or social media profile. Since some social media
 * providers allow multiple profiles/blogs/channels/etc. per account, this is its own Entity.
 */
readonly class Channel extends Value implements Entity, SerializableValue {
	use SerializableValueKit;

	public const NAMESPACE = '144af6d4-b4fb-4500-bb28-8e729cc7f585';

	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string $provider    Key for the handler this is tied to (usually provider name).
	 * @param string $providerKey Unique identifier for this account at this provider.
	 * @return Identifier ID constructed from provider and key.
	 */
	public static function buildId(string $provider, string $providerKey): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$provider|$providerKey");
	}

	/**
	 * Construct the Channel
	 *
	 * @param string          $provider     Key for the handler this is tied to (usually provider name).
	 * @param string          $providerKey  Unique identifier for this account at this provider.
	 * @param string          $displayName  Recognizable name for the channel (URL or handle?).
	 * @param array           $details      Information needed by the handler.
	 * @param Identifier|null $connectionId Connection needed to authenticate for this channel (if necessary).
	 */
	public function __construct(
		public readonly string $provider,
		public readonly string $providerKey,
		public readonly string $displayName,
		public readonly array $details,
		public readonly ?Identifier $connectionId = null,
	) {
	}

	/**
	 * Get the constructed ID from $provider and $providerKey
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier {
		return self::BuildId(provider: $this->provider, providerKey: $this->providerKey);
	}
}
