<?php

namespace Smolblog\Markdown;

use cebe\markdown\GithubMarkdown;

/**
 * Markdown parser with some extra Smolblog flair.
 */
class SmolblogMarkdown extends GithubMarkdown {
	public const PATTERN = '/^:\[(.+)\]\((.+)\)$/';

	/**
	 * Undocumented function
	 *
	 * @param EmbedProvider $embed EmbedProvider to provide embed codes.
	 */
	public function __construct(
		private EmbedProvider $embed
	) {
	}

	/**
	 * Identify an Embed directive.
	 *
	 * @param string $line Current line being parsed.
	 * @return boolean
	 */
	protected function identifyEmbed(string $line) {
		return preg_match(static::PATTERN, $line) === 1;
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
		preg_match(static::PATTERN, $lines[$current], $matches);

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
		$code = $this->embed->getEmbedCodeFor($block['url']);
		if (isset($code)) {
			return $code . "\n";
		}
		return '<p><a href="' . $block['url'] . '" target="_blank">' . $block['alt'] . "</a></p>\n";
	}
}
