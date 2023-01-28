<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Content\Media;
use Smolblog\Core\Post\{Block};
use Smolblog\Framework\Objects\Identifier;

/**
 * Include a single audio file in a Post
 */
class AudioBlock extends Block {
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
