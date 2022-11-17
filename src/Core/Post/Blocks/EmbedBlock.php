<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;

/**
 * Block to embed some external content through oEmbed
 */
class EmbedBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string      $url      URL of the content.
	 * @param string|null $response Cached response from the oEmbed endpoint.
	 */
	public function __construct(
		public readonly string $url,
		public readonly ?string $response = null
	) {
	}
}
