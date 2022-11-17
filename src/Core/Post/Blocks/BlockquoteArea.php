<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;

/**
 * Parent block that encloses the given blocks in a blockquote.
 */
class BlockquoteArea extends Block {
	/**
	 * Construct the block
	 *
	 * @param Block[] $content Inner blocks.
	 */
	public function __construct(
		public readonly array $content,
	) {
	}
}
