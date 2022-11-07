<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\{Block, Media};

/**
 * Display a single image in a Post
 */
class ImageBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param Media $media Media object for image to display.
	 */
	public function __construct(
		public readonly Media $media,
	) {
	}
}
