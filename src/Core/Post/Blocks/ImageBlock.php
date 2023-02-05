<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Content\Media;
use Smolblog\Core\Post\{Block};
use Smolblog\Framework\Objects\Identifier;

/**
 * Display a single image in a Post
 */
class ImageBlock extends Block {
	use MediaBlockSerializationToolkit;

	/**
	 * Construct the block
	 *
	 * @param Media      $media Media object for image to display.
	 * @param Identifier $id    ID for block if it exists.
	 */
	public function __construct(
		public readonly Media $media,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
