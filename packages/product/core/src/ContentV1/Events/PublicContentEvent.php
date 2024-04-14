<?php

namespace Smolblog\Core\ContentV1\Events;

use Smolblog\Core\ContentV1\ContentBuilderKit;
use Smolblog\Core\ContentV1\ContentBuilder;
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
abstract readonly class PublicContentEvent extends ContentEvent implements ContentBuilder {
	use ContentBuilderKit;

	/**
	 * Get the ID of the content in question.
	 *
	 * @return Identifier
	 */
	public function getContentId(): Identifier {
		return $this->contentId;
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
