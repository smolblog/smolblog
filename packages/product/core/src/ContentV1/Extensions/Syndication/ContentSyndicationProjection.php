<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\ConnectionInterface;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\NamedIdentifier;

/**
 * Store state related to content syndication.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class ContentSyndicationProjection implements Projection {
	public const TABLE = 'content_syndication';
	public const NAMESPACE = '1416cecc-4329-4f25-a535-61dbaf727531';

	/**
	 * Construct the projection.
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(private ConnectionInterface $db) {
	}

	/**
	 * Update the syndication channels for a piece of content.
	 *
	 * These will be used by the service when the content is published.
	 *
	 * @param SyndicationChannelsSet $event Event to handle.
	 * @return void
	 */
	public function onSyndicationChannelsSet(SyndicationChannelsSet $event) {
		// Delete any existing channels that are not present in the event.
		$this->db->table(self::TABLE)->
			where('content_uuid', '=', $event->contentId->toString())->
			whereNotNull('channel_uuid')->
			whereNull('url')->
			whereNotIn('channel_uuid', array_map(fn($id) => $id->toString(), $event->channels))->
			delete();

		$this->db->table(self::TABLE)->insertOrIgnore(array_map(
			fn($chId) => [
				'row_uuid' => $this->rowIdFor(contentId: $event->contentId, channelId: $chId),
				'content_uuid' => $event->contentId->toString(),
				'channel_uuid' => $chId,
			],
			$event->channels,
		));
	}

	/**
	 * Add a new link to the syndication state.
	 *
	 * @param ContentSyndicated $event Event to handle.
	 * @return void
	 */
	public function onContentSyndicated(ContentSyndicated $event) {
		$this->db->table(self::TABLE)->upsert(
			[
				'row_uuid' => $this->rowIdFor(
					contentId: $event->contentId,
					channelId: $event->channelId,
					url: $event->url
				),
				'content_uuid' => $event->contentId->toString(),
				'channel_uuid' => $event->channelId?->toString() ?? null,
				'url' => $event->url,
			],
			'row_uuid',
			['url'],
		);
	}

	/**
	 * Add syndication state to any messages that need it.
	 *
	 * @param NeedsSyndicationState $message Message to handle.
	 * @return void
	 */
	#[ExecutionLayerListener(later: 1)]
	public function onNeedsSyndicationState(NeedsSyndicationState $message) {
		$rows = $this->db->table(self::TABLE)->where('content_uuid', '=', $message->getContentId()->toString())->get();

		$channels = [];
		$links = [];
		foreach ($rows->all() as $row) {
			if (isset($row->url)) {
				$links[] = new SyndicationLink(
					url: $row->url,
					channelId: isset($row->channel_uuid) ? Identifier::fromString($row->channel_uuid) : null,
				);
				continue;
			}

			$channels[] = Identifier::fromString($row->channel_uuid);
		};

		$message->setSyndicationState(new Syndication(links: $links, channels: $channels));
	}

	/**
	 * Generate an ID based on the content ID, channel ID, and URL.
	 *
	 * We want to prioritize the content and channel IDs, but if the channel ID is not present, use the URL. This avoids
	 * having to worry about a UNIQUE constraint on a nullable field.
	 *
	 * @param Identifier      $contentId ID of the content.
	 * @param Identifier|null $channelId Optional channel ID.
	 * @param string|null     $url       Optional URL.
	 * @return Identifier
	 */
	public static function rowIdFor(
		Identifier $contentId,
		?Identifier $channelId = null,
		?string $url = null
	): Identifier {
		return new NamedIdentifier(
			namespace: self::NAMESPACE,
			name: $contentId->toString() . '|' . (isset($channelId) ? $channelId->toString() : $url),
		);
	}
}
