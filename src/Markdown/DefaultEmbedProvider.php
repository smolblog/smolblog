<?php

namespace Smolblog\Markdown;

use Embed\Embed;
use Throwable;

/**
 * Default EmbedProvider using Embed\Embed if it exists.
 *
 * If the Embed\Embed library is not installed, it will always return null for embed codes, and the parser will use
 * the fallback strategy.
 */
class DefaultEmbedProvider implements EmbedProvider {
	/**
	 * Internal copy of the Embed library.
	 *
	 * @var Embed|null
	 */
	private ?Embed $internal = null;

	/**
	 * Construct the provider.
	 */
	public function __construct() {
		if (class_exists(Embed::class)) {
			$this->internal = new Embed();
		}
	}

	/**
	 * Get the HTML embed code for the given URL.
	 *
	 * @param string $url URL to embed.
	 * @return string|null HTML code to embed the URL or null if not found.
	 */
	public function getEmbedCodeFor(string $url): ?string {
		if (isset($this->internal)) {
			try {
				$embedcode = $this->internal->get($url)->code;
				if (!empty($embedcode->html)) {
					return $embedcode->html;
				}
			} catch (Throwable $e) {
				// Ignore the exception or error and use the backup code below.
			}
		}
		return null;
	}
}
