<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\{Block, Media};

/**
 * Display a single video in a Post
 */
class VideoBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param Media $media Media object for video to display.
	 */
	public function __construct(
		public readonly Media $media,
	) {
	}
}
