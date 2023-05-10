<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content\Content;

/**
 * For events that indicate something has changed to the public-facing content.
 *
 * Changes to public-facing content are significant events that need their own callout. They trigger changes not only
 * to the public-facing website and feeds but also require push notifications, sending content to other channels,
 * and/or alerting ActivityPub subscribers.
 *
 * It is recommended to extend and listen to the child events, `PublicContentAdded`, `PublicContentChanged`, and
 * `PublicContentRemoved`.
 */
abstract class PublicContentEvent extends ContentEvent {
	/**
	 * The full Content object as of this event.
	 *
	 * @var Content
	 */
	protected Content $contentState;

	/**
	 * Get the state of the content as of this event.
	 *
	 * @return Content
	 */
	public function getContent(): Content {
		return $this->contentState;
	}

	/**
	 * Set the state of this content as of this event.
	 *
	 * @param Content $state Content object as of this event.
	 * @return void
	 */
	public function setContent(Content $state): void {
		$this->contentState = $state;
	}

	/**
	 * Empty payload (for now).
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [];
	}
}
