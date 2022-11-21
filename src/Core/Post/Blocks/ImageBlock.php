<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\{Block, Media};
use Smolblog\Framework\Identifier;

/**
 * Display a single image in a Post
 */
class ImageBlock extends Block {
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
