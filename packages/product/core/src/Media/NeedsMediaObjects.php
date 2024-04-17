<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Framework\Objects\Identifier;

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
