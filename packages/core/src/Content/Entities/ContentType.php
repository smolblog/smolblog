<?php

namespace Smolblog\Core\Content\Entities;

use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * Define what a Content Type needs to have, namely a title and a type.
 *
 * Even if the title isn't shown on the web or in feeds, it's still needed for administration views and such.
 */
interface ContentType extends Value {
	/**
	 * Key for this content type.
	 *
	 * @return string
	 */
	public static function getKey(): string;

	/**
	 * Get the title of the content.
	 *
	 * For use in the title tag, the list of content, and other places.
	 *
	 * @codeCoverageIgnore For some reason Xdebug insists this line is untested.
	 *
	 * @return string
	 */
	public string $title { get; }
}
