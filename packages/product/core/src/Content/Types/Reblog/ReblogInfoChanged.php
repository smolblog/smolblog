<?php

namespace Smolblog\Core\Content\Types\Reblog;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentBodyEdited;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates the Reblog's information about the external URL has been changed.
 */
class ReblogInfoChanged extends ContentBodyEdited {
	use ReblogEventKit;

	/**
	 * Construct the event.
	 *
	 * @param string                 $url       URL of the content being reblogged.
	 * @param ExternalContentInfo    $info      Updated external URL info.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $url,
		public readonly ExternalContentInfo $info,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Serialize this event's unique properties.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [ 'url' => $this->url, 'info' => $this->info->toArray() ];
	}

	/**
	 * Unserialize this event.
	 *
	 * @param array $payload Serialized payload.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		return [ 'url' => $payload['url'], 'info' => ExternalContentInfo::fromArray($payload['info']) ];
	}
}
