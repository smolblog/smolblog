<?php

namespace Smolblog\Core\Content\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * Define what a Content Type needs to have, namely a title and a type.
 *
 * Even if the title isn't shown on the web or in feeds, it's still needed for administration views and such.
 */
abstract readonly class ContentType extends Value implements SerializableValue {
	use SerializableSupertypeKit;

	public const KEY = '';

	/**
	 * Get the title of the content.
	 *
	 * For use in the title tag, the list of content, and other places.
	 *
	 * @codeCoverageIgnore For some reason Xdebug insists this line is untested.
	 *
	 * @return string
	 */
	abstract public function getTitle(): string;
}
