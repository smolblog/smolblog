<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use DateTimeInterface;
use Smolblog\Core\ContentV1\Events\ContentExtensionEdited;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a piece of content has been syndicated to the given URL.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class ContentSyndicated extends ContentExtensionEdited implements NeedsSyndicationState {
	/**
	 * Store the state of syndication on this content.
	 *
	 * @var Syndication
	 */
	private Syndication $state;

	/**
	 * Construct the event.
	 *
	 * @param string                 $url       URL to the external content.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $channelId Optional ID of the channel used to syndicate.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $url,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		public readonly ?Identifier $channelId = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the ID of the content in question.
	 *
	 * @return Identifier
	 */
	public function getContentId(): Identifier {
		return $this->contentId;
	}

	/**
	 * Store the current state of Syndication on this content.
	 *
	 * @param Syndication $state Current Syndication info.
	 * @return void
	 */
	public function setSyndicationState(Syndication $state) {
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
			'url' => $this->url,
			'channelId' => $this->channelId?->toString(),
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
			'url' => $payload['url'],
			'channelId' => self::safeDeserializeIdentifier($payload['channelId']),
		];
	}
}
