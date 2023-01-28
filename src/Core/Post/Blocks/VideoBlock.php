<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Content\Media as ContentMedia;
use Smolblog\Core\Post\{Block, Media};
use Smolblog\Framework\Objects\Identifier;

/**
 * Display a single video in a Post
 */
class VideoBlock extends Block {
	use MediaBlockSerializationToolkit;

	/**
	 * Construct the block
	 *
	 * @param ContentMedia $media Media object for video to display.
	 * @param Identifier   $id    ID for block if it exists.
	 */
	public function __construct(
		public readonly ContentMedia $media,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
