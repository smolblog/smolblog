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
	 * Consistently build a unique identifier out of the handler and key.
	 *
	 * @param string $handler    Key for the handler this is tied to (usually handler name).
	 * @param string $handlerKey Unique identifier for this account at this handler.
	 * @return Identifier ID constructed from handler and key.
	 */
	public static function buildId(string $handler, string $handlerKey): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$handler|$handlerKey");
	}

	/**
	 * Create the Connection.
	 *
	 * @param Identifier $userId      ID of the user this Connection belongs to.
	 * @param string     $handler     Slug for the Connector this is tied to.
	 * @param string     $handlerKey  Unique identifier for this account at this handler.
	 * @param string     $displayName Recognizable name for the account (username or email?).
	 * @param array      $details     Information needed by the handler.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly string $handler,
		public readonly string $handlerKey,
		public readonly string $displayName,
		public readonly array $details,
	) {
	}

	/**
	 * Get the constructed ID from $handler and $handlerKey
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier {
		return self::BuildId(handler: $this->handler, handlerKey: $this->handlerKey);
	}
}
