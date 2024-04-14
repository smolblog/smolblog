<?php

namespace Smolblog\Core\Federation;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * An external entity that should be notified of new content.
 *
 * As of now (June 2023), this is really only for ActivityPub followers. But the AT Protocol is on the horizon, and
 * there may be other protocols like Nostr that have similar needs. This could also represent a hub server that
 * aggregates and updates feeds.
 */
readonly class Follower extends Value implements Entity, SerializableValue {
	use SerializableValueKit;
	public const NAMESPACE = 'e8b82f68-39f8-4ace-9104-abf4fdc3187e';

	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string|Identifier $siteId      Site the follower is following.
	 * @param string            $provider    Slug for the FollowerProvider this is tied to (usually protocol name).
	 * @param string            $providerKey Unique identifier for this follower via this provider.
	 * @return Identifier ID constructed from provider and key.
	 */
	public static function buildId(string|Identifier $siteId, string $provider, string $providerKey): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$siteId|$provider|$providerKey");
	}

	/**
	 * Construct the entity.
	 *
	 * @param Identifier $siteId      Site this Follower is following.
	 * @param string     $provider    Slug for the FollowerProvider this is tied to (usually protocol name).
	 * @param string     $providerKey Unique identifier for this follower via this provider.
	 * @param string     $displayName Human-recognizable identifier for this follower.
	 * @param array      $details     Any data required for the FollowerProvider to work.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly string $provider,
		public readonly string $providerKey,
		public readonly string $displayName,
		public readonly array $details,
	) {
	}

	public function getId(): Identifier
	{
		return self::buildId(siteId: $this->siteId, provider: $this->provider, providerKey: $this->providerKey);
	}
}
