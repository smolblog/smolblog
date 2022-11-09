<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;

/**
 * Describes a heading tag (<h2> through <h6>)
 */
class HeadingBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string  $content Text of the heading.
	 * @param integer $level   Heading level 2-6, maps to h2-h6. Default 2.
	 */
	public function __construct(
		public readonly string $content,
		public readonly int $level = 2,
	) {
	}
}
