<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Entity;
use Smolblog\Framework\Identifier;

/**
 * Information about credentials needed to authenticate against an
 * exteral API as a particular user.
 */
readonly class Connection extends Entity {
	public const NAMESPACE = '3c7d4546-2086-44a0-aec8-85e64c6d2166';

	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string $provider    Slug for the Connector this is tied to (usually provider name).
	 * @param string $providerKey Unique identifier for this account at this provider.
	 * @return Identifier ID constructed from provider and key.
	 */
	public static function buildId(string $provider, string $providerKey): Identifier {
		return Identifier::createFromName(namespace: self::NAMESPACE, name: "$provider|$providerKey");
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
		public int $userId,
		public string $provider,
		public string $providerKey,
		public string $displayName,
		public array $details,
	) {
		parent::__construct(self::BuildId(provider: $provider, providerKey: $providerKey));
	}
}