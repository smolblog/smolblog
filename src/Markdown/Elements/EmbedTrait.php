<?php

namespace Smolblog\Markdown\Elements;

use Smolblog\Markdown\EmbedProvider;

trait EmbedTrait {
	/**
	 * An EmbedProvider that can provide an unpacked embed code to the parser.
	 *
	 * @var EmbedProvider|null
	 */
	protected ?EmbedProvider $embedProvider = null;

	public const EMBED_PATTERN = '/^:\[(.+)\]\((.+)\)$/';

	/**
	 * Identify an Embed directive.
	 *
	 * @param string $line Current line being parsed.
	 * @return boolean
	 */
	protected function identifyEmbed(string $line) {
		return preg_match(static::EMBED_PATTERN, $line) === 1;
	}

	/**
	 * Parse an embed directive.
	 *
	 * @param array   $lines   Array of lines of the Markdown document.
	 * @param integer $current Current line.
	 * @return array Parsing information.
	 */
	protected function consumeEmbed(array $lines, int $current) {
		$matches = array();
		preg_match(static::EMBED_PATTERN, $lines[$current], $matches);

		return [
			[
				'embed',
				'alt' => $matches[1],
				'url' => $matches[2]
			],
			$current + 1
		];
	}

	/**
	 * Render the embed code.
	 *
	 * @param array $block Block data.
	 * @return string
	 */
	protected function renderEmbed(array $block) {
		$code = $this->embedProvider?->getEmbedCodeFor($block['url']);
		if (isset($code)) {
			return $code . "\n";
		}
		return '<p><a href="' . $block['url'] . '" target="_blank">' . $block['alt'] . "</a></p>\n";
	}
}
