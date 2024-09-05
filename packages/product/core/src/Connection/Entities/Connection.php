<?php

namespace Smolblog\Core\Connection\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Information about credentials needed to authenticate against an
 * exteral API as a particular user.
 */
readonly class Connection extends Value implements Entity, SerializableValue {
	use SerializableValueKit;

	public const NAMESPACE = '3c7d4546-2086-44a0-aec8-85e64c6d2166';

	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string $provider    Slug for the Connector this is tied to (usually provider name).
	 * @param string $providerKey Unique identifier for this account at this provider.
	 * @return Identifier ID constructed from provider and key.
	 */
	public static function buildId(string $provider, string $providerKey): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$provider|$providerKey");
	}

	/**
	 * Create the Connection.
	 *
	 * @param Identifier $userId      ID of the user this Connection belongs to.
	 * @param string     $provider    Slug for the Connector this is tied to.
	 * @param string     $providerKey Unique identifier for this account at this provider.
	 * @param string     $displayName Recognizable name for the account (username or email?).
	 * @param array      $details     Information to store.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly string $provider,
		public readonly string $providerKey,
		public readonly string $displayName,
		public readonly array $details,
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
