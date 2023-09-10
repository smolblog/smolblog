<?php

namespace Smolblog\Core\Content\Fields;

use Smolblog\Framework\Objects\Value;

/**
 * A text field containing Markdown-formatted text.
 */
class Markdown extends Value {
	/**
	 * Construct the field.
	 *
	 * @param string $text Markdown-formatted text.
	 */
	public function __construct(
		public readonly string $text,
	) {
	}
}
