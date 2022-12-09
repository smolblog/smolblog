<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * Block to embed some external content through oEmbed
 */
readonly class EmbedBlock extends Block {
	/**
	 * Construct the block
	 *
	 * @param string      $url      URL of the content.
	 * @param string|null $response Cached response from the oEmbed endpoint.
	 * @param Identifier  $id       ID for block if it exists.
	 */
	public function __construct(
		public readonly string $url,
		public readonly ?string $response = null,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
