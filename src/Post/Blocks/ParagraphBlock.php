<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;

/**
 * A basic paragraph block.
 */
class ParagraphBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string $content HTML-formatted contents of the block.
	 * @param array  $styles  Any CSS classes to add to the surrounding tag.
	 */
	public function __construct(
		public readonly string $content,
		public readonly array $styles
	) {
	}
}
