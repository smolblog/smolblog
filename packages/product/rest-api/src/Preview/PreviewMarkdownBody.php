<?php

namespace Smolblog\Api\Preview;

use Smolblog\Framework\Objects\Value;

/**
 * Body for a Markdown preview request.
 */
class PreviewMarkdownBody extends Value {
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
