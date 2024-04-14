<?php

namespace Smolblog\Core\ContentV1;

use Smolblog\Framework\Objects\SerializeableValue;

/**
 * Define what a Content Type needs to have, namely a title and a body.
 *
 * Even if the title isn't shown on the web or in feeds, it's still needed for administration views and such.
 */
interface ContentType extends SerializeableValue {
	/**
	 * Get the title of the content.
	 *
	 * For use in the title tag, the list of content, and other places.
	 *
	 * @return string
	 */
	public function getTitle(): string;

	/**
	 * Get the HTML-formatted content body.
	 *
	 * @return string
	 */
	public function getBodyContent(): string;

	/**
	 * Get the key for this type.
	 *
	 * @return string
	 */
	public function getTypeKey(): string;
}
