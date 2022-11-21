<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * A singular pull quote with an optional caption.
 */
class QuoteBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string      $content  Content of the quote.
	 * @param string|null $citation Optional citation. HTML OK.
	 * @param Identifier  $id       ID for block if it exists.
	 */
	public function __construct(
		public readonly string $content,
		public readonly ?string $citation = null,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
