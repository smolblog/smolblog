<?php

namespace Smolblog\Core\Content\Entities;

use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * Define what a Content Type needs to have, namely a title and a type.
 *
 * Even if the title isn't shown on the web or in feeds, it's still needed for administration views and such.
 */
abstract readonly class ContentType implements Value {
	use ValueKit;

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
