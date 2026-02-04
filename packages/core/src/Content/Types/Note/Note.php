<?php

namespace Smolblog\Core\Content\Types\Note;

use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Foundation\Value\ValueKit;
use Crell\Serde\Attributes\Field;
use Smolblog\Core\Content\ContentUtilities;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * A short, text-only message. Like a tweet.
 */
class Note implements ContentType {
	use ValueKit;

	public static function getKey(): string {
		return 'note';
	}

	/**
	 * Construct the Note.
	 *
	 * @param Markdown $text Markdown-formatted text of the Note.
	 */
	public function __construct(
		public readonly Markdown $text,
	) {}

	/**
	 * Create the title by truncating the text.
	 *
	 * @return string
	 */
	#[Field(exclude: true)]
	public string $title {
		get => ContentUtilities::truncateText(strval($this->text));
	}
}
