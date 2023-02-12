<?php

namespace Smolblog\Core\Content\Extensions\SyndicationLinks;

use DateTimeInterface;
use Smolblog\Core\Content\ContentExtension;
use Smolblog\Core\Content\Events\ContentExtensionEdited;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a piece of content has been syndicated to the given URL.
 */
class ContentSyndicated extends ContentExtensionEdited {
	/**
	 * Store the state of all SyndicationLinks on this content.
	 *
	 * @var SyndicationLinks
	 */
	private SyndicationLinks $links;

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
	 * Store the current state of all SyndicationLinks on this content.
	 *
	 * @param SyndicationLinks $links Links on this content, including this one.
	 * @return void
	 */
	public function setLinks(SyndicationLinks $links) {
		$this->links = $links;
	}

	/**
	 * Get the current state of the SyndicationLinks as of this event.
	 *
	 * @return SyndicationLinks
	 */
	public function getNewExtension(): SyndicationLinks {
		return $this->links;
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
