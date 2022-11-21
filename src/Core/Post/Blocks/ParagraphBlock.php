<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * A basic paragraph block.
 */
class ParagraphBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string     $content HTML-formatted contents of the block.
	 * @param array      $styles  Any CSS classes to add to the surrounding tag.
	 * @param Identifier $id      ID for block if it exists.
	 */
	public function __construct(
		public readonly string $content,
		public readonly array $styles = [],
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
