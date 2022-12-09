<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\{Block, Media};
use Smolblog\Framework\Identifier;

/**
 * Include a single audio file in a Post
 */
readonly class AudioBlock extends Block {
	use MediaBlockSerializationToolkit;

	/**
	 * Construct the block
	 *
	 * @param Media      $media Media object for audio file to include.
	 * @param Identifier $id    ID for block if it exists.
	 */
	public function __construct(
		public readonly Media $media,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
