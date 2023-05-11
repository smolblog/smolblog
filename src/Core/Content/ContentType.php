<?php

namespace Smolblog\Core\Content;

/**
 * Define what a Content Type needs to have, namely a title and a body.
 *
 * Even if the title isn't shown on the web or in feeds, it's still needed for administration views and such.
 */
interface ContentType {
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
}
