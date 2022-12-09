<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * Describes a heading tag (<h2> through <h6>)
 */
readonly class HeadingBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string     $content Text of the heading.
	 * @param integer    $level   Heading level 2-6, maps to h2-h6. Default 2.
	 * @param Identifier $id      ID for block if it exists.
	 */
	public function __construct(
		public string $content,
		public int $level = 2,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}