<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * Block to represent that this is a Reblog-style post. Contains either a link or embed block for markup.
 */
class ReblogBlock extends Block {
	/**
	 * Construct the block. Either embed or link is required.
	 *
	 * @param string          $url       URL of the web page being reblogged.
	 * @param boolean         $showEmbed True to show embed block; false to show link block.
	 * @param EmbedBlock|null $embed     Embed block.
	 * @param LinkBlock|null  $link      Link block.
	 * @param Identifier      $id        ID for block if it exists.
	 */
	public function __construct(
		public readonly string $url,
		public readonly bool $showEmbed = true,
		public readonly ?EmbedBlock $embed = null,
		public readonly ?LinkBlock $link = null,
		Identifier $id = null,
	) {
		parent::__construct(id: $id);
	}
}
