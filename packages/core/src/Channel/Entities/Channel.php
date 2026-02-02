<?php

namespace Smolblog\Core\Channel\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Crell\Serde\Attributes\ClassNameTypeMap;
use Crell\Serde\Attributes\Field;
use Ramsey\Uuid\UuidInterface;

/**
 * Represents a single content channel, such as a blog, RSS feed, or social media profile. Since some social media
 * providers allow multiple profiles/blogs/channels/etc. per account, this is its own Entity.
 */
#[ClassNameTypeMap(key: 'class')]
abstract class Channel implements Entity, Value {
	use ValueKit;

	public const NAMESPACE = '144af6d4-b4fb-4500-bb28-8e729cc7f585';

	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string $handler    Key for the handler this is tied to (usually provider name).
	 * @param string $handlerKey Unique identifier for this account at this provider.
	 * @return UuidInterface ID constructed from provider and key.
	 */
	public static function buildId(string $handler, string $handlerKey): UuidInterface {
		return UuidFactory::named(namespace: self::NAMESPACE, name: "$handler|$handlerKey");
	}

	/**
	 * Construct the Channel
	 *
	 * @param string          $handler      Key for the handler this is tied to (usually provider name).
	 * @param string          $handlerKey   Unique identifier for this account at this provider.
	 * @param string          $displayName  Recognizable name for the channel (URL or handle?).
	 * @param UuidInterface|null $userId       User responsible for this Channel (if applicable).
	 * @param UuidInterface|null $connectionId Connection needed to authenticate for this channel (if necessary).
	 */
	public function __construct(
		public string $handler,
		public string $handlerKey,
		public string $displayName,
		public ?UuidInterface $userId = null,
		public ?UuidInterface $connectionId = null,
	) {}

	/**
	 * Get the constructed ID from $handler and $handlerKey
	 *
	 * @var UuidInterface
	 */
	#[Field(exclude: true)]
	public UuidInterface $id {
		get => self::buildId(handler: $this->handler, handlerKey: $this->handlerKey);
	}
}
