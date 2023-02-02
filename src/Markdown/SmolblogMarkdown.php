<?php

namespace Smolblog\Markdown;

use cebe\markdown\block\FencedCodeTrait;
use cebe\markdown\Markdown;
use Smolblog\Markdown\Elements\EmbedTrait;

/**
 * Markdown parser with some extra Smolblog flair.
 */
class SmolblogMarkdown extends Markdown {
	use FencedCodeTrait;
	use EmbedTrait;

	/**
	 * Construct the parser;
	 *
	 * @param EmbedProvider $embedProvider EmbedProvider to provide embed codes.
	 */
	public function __construct(
		EmbedProvider $embedProvider
	) {
		$this->embedProvider = $embedProvider;
		$this->html5 = true;
	}
}
