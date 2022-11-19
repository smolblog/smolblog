<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Entity;

/**
 * Information about credentials needed to authenticate against an
 * exteral API as a particular user.
 */
class Connection extends Entity {
	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string $provider    Slug for the Connector this is tied to (usually provider name).
	 * @param string $providerKey Unique identifier for this account at this provider.
	 * @return string ID constructed from provider and key.
	 */
	public static function buildId(string $provider, string $providerKey): string {
		return "$provider|$providerKey";
	}

	/**
	 * Create the Connection.
	 *
	 * @param integer $userId      ID of the user this Connection belongs to.
	 * @param string  $provider    Slug for the Connector this is tied to.
	 * @param string  $providerKey Unique identifier for this account at this provider.
	 * @param string  $displayName Recognizable name for the account (username or email?).
	 * @param array   $details     Information to store.
	 */
	public function __construct(
		public readonly int $userId,
		public readonly string $provider,
		public readonly string $providerKey,
		public readonly string $displayName,
		public readonly array $details,
	) {
		parent::__construct(self::BuildId(provider: $provider, providerKey: $providerKey));
	}
}
