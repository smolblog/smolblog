<?php

namespace Smolblog\Core\Content\Types\Reblog;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentBodyEdited;
use Smolblog\Core\Content\Markdown\NeedsMarkdownRendered;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates that the comment on a reblog has changed.
 */
class ReblogCommentChanged extends ContentBodyEdited implements NeedsMarkdownRendered {
	use ReblogEventKit;

	/**
	 * Store the ExternalContentInfo for the reblogged URL so we can provide a fully updated body.
	 *
	 * @var ExternalContentInfo
	 */
	private ExternalContentInfo $info;

	/**
	 * Construct the event.
	 *
	 * @param string                 $comment   New comment text in Markdown.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $comment,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Allow a projection to set the ExternalContentInfo.
	 *
	 * @param ExternalContentInfo $info Info about the reblogged URL.
	 * @return void
	 */
	public function setInfo(ExternalContentInfo $info) {
		$this->info = $info;
	}

	/**
	 * Serialize this event's unique properties.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [ 'comment' => $this->comment ];
	}
}
