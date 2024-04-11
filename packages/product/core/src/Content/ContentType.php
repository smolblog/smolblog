<?php

namespace Smolblog\Core\Content;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableSupertype;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;

/**
 * Define what a Content Type needs to have, namely a title and a type.
 *
 * Even if the title isn't shown on the web or in feeds, it's still needed for administration views and such.
 */
abstract readonly class ContentType extends Value implements SerializableSupertype {
	use SerializableSupertypeKit;

	/**
	 * Get the title of the content.
	 *
	 * For use in the title tag, the list of content, and other places.
	 *
	 * @return string
	 */
	abstract public function getTitle(): string;

	/**
	 * Get the key for this type.
	 *
	 * @return string
	 */
	abstract public function getTypeKey(): string;
}
