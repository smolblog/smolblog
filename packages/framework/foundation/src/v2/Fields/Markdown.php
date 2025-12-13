<?php

namespace Smolblog\Foundation\v2\Fields;

use Smolblog\Foundation\v2\Value;
use Smolblog\Foundation\v2\Value\CloneKit;

/**
 * Represents a block of Markdown text.
 *
 * Is it a class that just has a text field? Yes. Does it turn the Markdown into HTML? No, that needs a Service. So why
 * does this class exist? Because Markdown text should be treated differently from generic text, especially in the
 * user interface.
 */
readonly class Markdown implements Value, Field {
	use CloneKit;

	/**
	 * Create the field.
	 *
	 * @param string $text Markdown-formatted text.
	 */
	public function __construct(public string $text) {
	}

	/**
	 * Convert the field to a string.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->text;
	}

	/**
	 * Create the field from a string.
	 *
	 * @param string $string Markdown-formatted text.
	 * @return self
	 */
	public static function fromString(string $string): static {
		return new self($string);
	}
}
