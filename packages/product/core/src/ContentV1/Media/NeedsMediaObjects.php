<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates that a message needs objects for its media IDs.
 */
interface NeedsMediaObjects {
	/**
	 * Get the Media IDs
	 *
	 * @return Identifier[]
	 */
	public function getMediaIds(): array;

	/**
	 * Set the rendered HTML
	 *
	 * @param Identifier[] $objects Rendered HTML.
	 * @return void
	 */
	public function setMediaObjects(array $objects): void;
}
