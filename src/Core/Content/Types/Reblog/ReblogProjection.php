<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Framework\Messages\Attributes\ContentBuildLayerListener;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Projection;

/**
 * Store Reblog-specific state.
 */
class ReblogProjection implements Projection {
	public const TABLE = 'reblogs';

	/**
	 * Create the Projection.
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(
		private ConnectionInterface $db
	) {
	}

	/**
	 * Create a new Reblog entry.
	 *
	 * @param ReblogCreated $event Event to handle.
	 * @return void
	 */
	public function onReblogCreated(ReblogCreated $event) {
		$this->db->table(self::TABLE)->insert([
			'content_uuid' => $event->contentId->toString(),
			'url' => $event->url,
			'comment' => $event->comment,
			'comment_html' => $event->getCommentHtml(),
			'url_info' => isset($event->info) ? json_encode($event->info) : null,
		]);
	}

	/**
	 * Update the URL and info for a Reblog
	 *
	 * @param ReblogInfoChanged $event Event to handle.
	 * @return void
	 */
	#[ExecutionLayerListener(earlier: 1)]
	public function onReblogInfoChanged(ReblogInfoChanged $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'url' => $event->url,
			'url_info' => json_encode($event->info),
		]);
		$event->setMarkdownHtml([
			$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->value('comment_html')
		]);
	}

	/**
	 * Update the comment for a Reblog
	 *
	 * @param ReblogCommentChanged $event Event to handle.
	 * @return void
	 */
	#[ExecutionLayerListener(earlier: 1)]
	public function onReblogCommentChanged(ReblogCommentChanged $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'comment' => $event->comment,
			'comment_html' => $event->getCommentHtml(),
		]);
		$event->setInfo(
			ExternalContentInfo::jsonDeserialize(
				$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->value('url_info')
			)
		);
	}

	/**
	 * Delete a Reblog.
	 *
	 * @param ReblogDeleted $event Event to handle.
	 * @return void
	 */
	public function onReblogDeleted(ReblogDeleted $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->delete();
	}

	/**
	 * Add a Reblog to a ContentBuilder.
	 *
	 * @param ReblogBuilder $message Message to handle.
	 * @return void
	 */
	#[ContentBuildLayerListener()]
	public function buildReblog(ReblogBuilder $message) {
		$row = $this->db->table(self::TABLE)->where('content_uuid', '=', $message->getContentId()->toString())->first();

		$message->setContentType(new Reblog(
			url: $row->url,
			comment: $row->comment ?? null,
			info: isset($row->url_info) ? ExternalContentInfo::jsonDeserialize($row->url_info) : null,
			commentHtml: $row->comment_html ?? null,
		));
	}
}
