<?php

namespace Smolblog\Core\Connection\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Crell\Serde\Attributes\Field;
use Ramsey\Uuid\UuidInterface;

/**
 * Information about credentials needed to authenticate against an
 * exteral API as a particular user.
 */
class Connection implements Value, Entity {
	use ValueKit;

	public const NAMESPACE = '3c7d4546-2086-44a0-aec8-85e64c6d2166';

	/**
	 * Consistently build a unique identifier out of the handler and key.
	 *
	 * @param string $handler    Key for the handler this is tied to (usually handler name).
	 * @param string $handlerKey Unique identifier for this account at this handler.
	 * @return UuidInterface ID constructed from handler and key.
	 */
	public static function buildId(string $handler, string $handlerKey): UuidInterface {
		return UuidFactory::named(namespace: self::NAMESPACE, name: "$handler|$handlerKey");
	}

	/**
	 * Create the Connection.
	 *
	 * @param UuidInterface $userId      ID of the user this Connection belongs to.
	 * @param string     $handler     Slug for the Connector this is tied to.
	 * @param string     $handlerKey  Unique identifier for this account at this handler.
	 * @param string     $displayName Recognizable name for the account (username or email?).
	 * @param array      $details     Information needed by the handler.
	 */
	public function __construct(
		public readonly UuidInterface $userId,
		public readonly string $handler,
		public readonly string $handlerKey,
		public readonly string $displayName,
		#[MapType('mixed')] public readonly array $details,
	) {}

	/**
	 * Get the constructed ID from $handler and $handlerKey
	 *
	 * @var UuidInterface
	 */
	#[Field(exclude: true)]
	public UuidInterface $id {
		get => self::BuildId(handler: $this->handler, handlerKey: $this->handlerKey);
	}
}
