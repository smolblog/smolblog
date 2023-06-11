<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentExtensionEdited;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates the channels content should syndicate to are set.
 */
class SyndicationChannelsSet extends ContentExtensionEdited {
	/**
	 * Store the state of syndication on this content.
	 *
	 * @var Syndication
	 */
	private Syndication $state;

	/**
	 * Construct the event.
	 *
	 * @param Identifier[]           $channels  Channels this content should syndicate to.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly array $channels,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Store the current state of Syndication on this content.
	 *
	 * @param Syndication $state Current Syndication info.
	 * @return void
	 */
	public function setState(Syndication $state) {
		$this->state = $state;
	}

	/**
	 * Get the current state of the Syndication as of this event.
	 *
	 * @return Syndication
	 */
	public function getNewExtension(): Syndication {
		return $this->state;
	}

	/**
	 * Get this event's unique fields.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'channels' => array_map(fn($channel) => $channel->toString(), $this->channels),
		];
	}

	/**
	 * Deserialize this event's payload.
	 *
	 * @param array $payload Serialized payload.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		return [
			'channels' => array_map(fn($channel) => self::safeDeserializeIdentifier($channel), $payload['channels']),
		];
	}
}
