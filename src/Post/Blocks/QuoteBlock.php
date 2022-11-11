<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;

/**
 * A singular pull quote with an optional caption.
 */
class QuoteBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string      $content  Content of the quote.
	 * @param string|null $citation Optional citation. HTML OK.
	 */
	public function __construct(
		public readonly string $content,
		public readonly ?string $citation = null,
	) {
	}
}
