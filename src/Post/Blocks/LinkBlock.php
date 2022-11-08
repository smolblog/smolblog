<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;

/**
 * A link and some supporting markup. Designed to either show an opengraph-like summary or a Daring Fireball-like
 * caption-and-quote.
 */
class LinkBlock extends Block {
	/**
	 * Create the block.
	 *
	 * @param string      $url              URL to link to.
	 * @param string      $title            Text of the link (such as the linked page's title).
	 * @param string|null $summary          Optional; one-sentance description of the page.
	 * @param string|null $thumbnailUrl     Optional; thumbnail or featured image for the page.
	 * @param string|null $pullQuote        Optional; relevant quoted text. HTML OK.
	 * @param string|null $pullQuoteCaption Optional; caption or attribution for quoted text.
	 */
	public function __construct(
		public readonly string $url,
		public readonly string $title,
		public readonly ?string $summary = null,
		public readonly ?string $thumbnailUrl = null,
		public readonly ?string $pullQuote = null,
		public readonly ?string $pullQuoteCaption = null
	) {
	}
}
