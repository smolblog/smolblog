<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\{Block, Media};
use Smolblog\Framework\Identifier;

/**
 * Display a single video in a Post
 */
readonly class VideoBlock extends Block {
	use MediaBlockSerializationToolkit;

	/**
	 * Construct the block
	 *
	 * @param Media      $media Media object for video to display.
	 * @param Identifier $id    ID for block if it exists.
	 */
	public function __construct(
		public Media $media,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
