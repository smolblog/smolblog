<?php

namespace Smolblog\Markdown;

use cebe\markdown\Markdown;
use Smolblog\Markdown\Elements\CustomFencedCodeTrait;
use Smolblog\Markdown\Elements\EmbedTrait;

/**
 * Markdown parser with some extra Smolblog flair.
 */
class SmolblogMarkdown extends Markdown {
	use CustomFencedCodeTrait;
	use EmbedTrait;

	/**
	 * Construct the parser;
	 *
	 * @param EmbedProvider $embedProvider EmbedProvider to provide embed codes.
	 */
	public function __construct(
		?EmbedProvider $embedProvider = null
	) {
		$this->embedProvider = $embedProvider;
		$this->html5 = true;
	}

	/**
	 * Add a custom handler to the service.
	 *
	 * @param string   $language Language this handler is for.
	 * @param callable $handler  Callable handler for the language.
	 * @return void
	 */
	public function addCustomCodeHandler(string $language, callable $handler): void {
		$this->codeRenderers[$language] = $handler;
	}
}
