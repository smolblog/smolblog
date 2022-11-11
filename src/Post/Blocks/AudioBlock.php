<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\{Block, Media};

/**
 * Include a single audio file in a Post
 */
class AudioBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param Media $media Media object for audio file to include.
	 */
	public function __construct(
		public readonly Media $media,
	) {
	}
}
