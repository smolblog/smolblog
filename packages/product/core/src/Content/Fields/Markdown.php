<?php

namespace Smolblog\Core\Content\Fields;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Field;
use Smolblog\Foundation\Value\Traits\FieldKit;

/**
 * Represents a block of Markdown text.
 *
 * Is it a class that just has a text field? Yes. Does it turn the Markdown into HTML? No, that needs a Service. So why
 * does this class exist? Because Markdown text should be treated differently from generic text, especially in the
 * user interface.
 */
readonly class Markdown extends Value implements Field {
	use FieldKit;

	public function __construct(public string $text) {
	}

	public function toString(): string {
		return $this->text;
	}

	public static function fromString(string $string): static {
		return new static($string);
	}
}
