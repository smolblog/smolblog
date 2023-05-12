<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content\ContentBuilderKit;
use Smolblog\Core\Content\ContentBuilder;

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
abstract class PublicContentEvent extends ContentEvent implements ContentBuilder {
	use ContentBuilderKit;

	/**
	 * Empty payload (for now).
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [];
	}
}
