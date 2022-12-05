<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Post\Block;
use Smolblog\Framework\Identifier;

/**
 * Block to represent that this is a Reblog-style post. Contains either a link or embed block for markup.
 */
class ReblogBlock extends Block {
	/**
	 * True if the Embed block should be shown; false to use the Link block.
	 *
	 * Automatically true if a Link block is not provided.
	 *
	 * @var boolean
	 */
	public readonly bool $showEmbed;

	/**
	 * Embed block for this reblog.
	 *
	 * If not provided to the constructor, created from $url.
	 *
	 * @var EmbedBlock
	 */
	public readonly EmbedBlock $embed;

	/**
	 * Construct the block. Either embed or link is required.
	 *
	 * @param string          $url       URL of the web page being reblogged.
	 * @param boolean         $showEmbed True to show embed block; false to show link block.
	 * @param EmbedBlock|null $embed     Embed block Uses URL if not provided.
	 * @param LinkBlock|null  $link      Link block.
	 * @param Identifier|null $id        ID for block if it exists.
	 */
	public function __construct(
		public readonly string $url,
		bool $showEmbed = true,
		?EmbedBlock $embed = null,
		public readonly ?LinkBlock $link = null,
		Identifier $id = null,
	) {
		$this->embed = $embed ?? new EmbedBlock(url: $url);
		$this->showEmbed = $showEmbed || !isset($this->link);
		parent::__construct(id: $id);
	}
}
