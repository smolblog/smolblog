<?php

namespace Smolblog\Markdown;

use cebe\markdown\GithubMarkdown;
use Embed\Embed;
use Exception;

/**
 * Markdown parser with some extra Smolblog flair.
 */
class SmolblogMarkdown extends GithubMarkdown {
	private $pattern = '/^:\[(.+)\]\((.+)\)$/';

	public function __construct(
		private Embed $embed
	)
	{

	}

	/**
	 * Identify an Embed directive.
	 *
	 * @param string $line Current line being parsed.
	 * @return boolean
	 */
	protected function identifyEmbed($line) {
		return preg_match($this->pattern, $line) === 1;
	}

	protected function consumeEmbed($lines, $current) {
		$matches = array();
		preg_match($this->pattern, $lines[$current], $matches);

		return [
			[
				'embed',
				'alt' => $matches[1],
				'url' => $matches[2]
			],
			$current + 1
		];
	}

	protected function renderEmbed($block) {
		try {
			$embedcode = $this->embed->get($block['url'])->code;
			if (!empty($embedcode->html)) {
				return $embedcode->html . "\n";
			}
		} catch (Exception $e) {
			// Ignore the exception and use the backup code below.
		}
		return '<p><a href="' . $block['url'] . '" target="_blank">' . $block['alt'] . "</a></p>\n";
	}
}
