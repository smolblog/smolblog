<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * Block to represent an HTML list
 */
readonly class ListBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string     $content HTML list content.
	 * @param boolean    $ordered True if this is an ordered (numbered) list.
	 * @param Identifier $id      ID for block if it exists.
	 */
	public function __construct(
		public string $content,
		public bool $ordered = false,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
