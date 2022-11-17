<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;

/**
 * Block to represent an HTML list
 */
class ListBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string  $content HTML list content.
	 * @param boolean $ordered True if this is an ordered (numbered) list.
	 */
	public function __construct(
		public readonly string $content,
		public readonly bool $ordered = false
	) {
	}
}
