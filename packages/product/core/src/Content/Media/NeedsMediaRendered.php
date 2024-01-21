<?php

namespace Smolblog\Core\Content\Media;

/**
 * Indicates that a message needs HTML for its media object[s].
 */
interface NeedsMediaRendered {
	/**
	 * Get the Media objects
	 *
	 * @return Media[]
	 */
	public function getMediaObjects(): array;

	/**
	 * Set the rendered HTML
	 *
	 * @param string[] $html Rendered HTML.
	 * @return void
	 */
	public function setMediaHtml(array $html): void;
}
