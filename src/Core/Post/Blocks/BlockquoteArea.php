<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * Parent block that encloses the given blocks in a blockquote.
 */
readonly class BlockquoteArea extends Block {
	/**
	 * Construct the block
	 *
	 * @param Block[]    $content Inner blocks.
	 * @param Identifier $id      ID for block if it exists.
	 */
	public function __construct(
		public array $content,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}