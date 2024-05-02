<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content;
use Smolblog\Foundation\Value\Fields\Identifier;

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
abstract readonly class PublicContentEvent extends BaseContentEvent {
	/**
	 * Get the ID of the content in question.
	 *
	 * @deprecated use $this->content->id
	 * @codeCoverageIgnore
	 *
	 * @return Identifier
	 */
	public function getContentId(): Identifier {
		return $this->entityId;
	}

	/**
	 * Get the content for this event.
	 *
	 * @deprecated Use $this->content.
	 * @codeCoverageIgnore
	 *
	 * @return Content
	 */
	public function getContent(): Content {
		return $this->content;
	}
}
