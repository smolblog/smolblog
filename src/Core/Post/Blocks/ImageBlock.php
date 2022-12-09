<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\{Block, Media};
use Smolblog\Framework\Identifier;

/**
 * Display a single image in a Post
 */
readonly class ImageBlock extends Block {
	use MediaBlockSerializationToolkit;

	/**
	 * Construct the block
	 *
	 * @param Media      $media Media object for image to display.
	 * @param Identifier $id    ID for block if it exists.
	 */
	public function __construct(
		public Media $media,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}