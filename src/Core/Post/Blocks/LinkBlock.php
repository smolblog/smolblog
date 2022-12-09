<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * A link and some supporting markup. Designed to either show an opengraph-like summary or a Daring Fireball-like
 * caption-and-quote.
 */
readonly class LinkBlock extends Block {
	/**
	 * Create the block.
	 *
	 * @param string      $url              URL to link to.
	 * @param string      $title            Text of the link (such as the linked page's title).
	 * @param string|null $summary          Optional; one-sentance description of the page.
	 * @param string|null $thumbnailUrl     Optional; thumbnail or featured image for the page.
	 * @param string|null $pullQuote        Optional; relevant quoted text. HTML OK.
	 * @param string|null $pullQuoteCaption Optional; caption or attribution for quoted text.
	 * @param Identifier  $id               ID for block if it exists.
	 */
	public function __construct(
		public string $url,
		public string $title,
		public ?string $summary = null,
		public ?string $thumbnailUrl = null,
		public ?string $pullQuote = null,
		public ?string $pullQuoteCaption = null,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
