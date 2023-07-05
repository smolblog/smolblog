<?php

namespace Smolblog\Api\Content;

use Smolblog\Framework\Objects\Value;

/**
 * Body for the CreateNote endpoint.
 */
class CreateNotePayload extends Value {
	/**
	 * Construct the payload.
	 *
	 * @param string  $text    SFMD-formatted text of the note.
	 * @param boolean $publish True to publish immediately.
	 */
	public function __construct(
		public readonly string $text,
		public readonly bool $publish,
	) {
	}
}
