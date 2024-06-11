<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Type\ContentType;

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
	) {
	}

	/**
	 * Create the title by truncating the text.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return ContentUtilities::truncateText(strval($this->text));
	}
}
