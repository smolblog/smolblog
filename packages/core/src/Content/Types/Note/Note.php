<?php

namespace Smolblog\Core\Content\Types\Note;

use Cavatappi\Foundation\Fields\Markdown;
use Smolblog\Core\Content\ContentUtilities;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * A short, text-only message. Like a tweet.
 */
readonly class Note extends ContentType {
	public const KEY = 'note';

	/**
	 * Construct the Note.
	 *
	 * @param Markdown $text Markdown-formatted text of the Note.
	 */
	public function __construct(
		public Markdown $text,
	) {}

	/**
	 * Create the title by truncating the text.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return ContentUtilities::truncateText(strval($this->text));
	}
}
