<?php

namespace Smolblog\Core\ContentV1\Extensions\Tags;

use Smolblog\Foundation\Value;

/**
 * An individual tag.
 */
readonly class Tag extends Value {
	/**
	 * The visible text of the tag.
	 *
	 * @var string
	 */
	public readonly string $text;

	/**
	 * The normalized, url-friendly text of the tag.
	 *
	 * @var string
	 */
	public readonly string $normalized;

	/**
	 * Construct the tag.
	 *
	 * @param string $text       The visible text of the tag.
	 * @param string $normalized The normalized, url-friendly text of the tag.
	 */
	public function __construct(string $text, ?string $normalized = null) {
		$this->text = trim($text);
		$this->normalized = $normalized ?? Tags::normalizeTagText($text);
	}
}
