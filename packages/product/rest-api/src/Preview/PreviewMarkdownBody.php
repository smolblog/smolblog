<?php

namespace Smolblog\Api\Preview;

use Smolblog\Foundation\Value;

/**
 * Body for a Markdown preview request.
 */
readonly class PreviewMarkdownBody extends Value {
	/**
	 * Construct the object.
	 *
	 * @param string $sfmd Smolblog-flavored Markdown to parse.
	 */
	public function __construct(
		public readonly string $sfmd
	) {
	}
}
