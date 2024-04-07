<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use DateTimeInterface;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentType;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\InvalidContentException;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * A short, title-less post.
 *
 * While a comparison could be drawn to what the Iconfactory first called a "tweet", this is closer to a
 * Mastodon "toot" or a Micro.blog post in that it allows some basic formatting.
 */
class Note implements ContentType {
	use SerializableKit;

	/**
	 * Truncated the given text to a given number of characters.
	 *
	 * @param string  $text  Text to truncate.
	 * @param integer $limit Line limit; default 100.
	 * @return string
	 */
	public static function truncateText(string $text, int $limit = 100): string {
		$truncated = substr($text, 0, strpos(wordwrap($text, $limit) . "\n", "\n"));
		if (strlen($text) > $limit) {
			$truncated .= '...';
		}
		return $truncated;
	}

	/**
	 * Get a title-appropriate truncation of the content.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return self::truncateText($this->text);
	}

	/**
	 * Get the HTML-formatted content of the note.
	 *
	 * @throws InvalidContentException Thrown if the rendered HTML has not been set on this object.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		if (!isset($this->rendered)) {
			throw new InvalidContentException("Rendered HTML has not been provided.");
		}
		return $this->rendered;
	}

	/**
	 * Get the type key ('note').
	 *
	 * @return string
	 */
	public function getTypeKey(): string {
		return 'note';
	}

	/**
	 * Construct this content object
	 *
	 * @param string      $text     Markdown-formatted text of the content.
	 * @param string|null $rendered Rendered HTML of the content.
	 */
	public function __construct(
		public readonly string $text,
		private ?string $rendered = null,
	) {
	}

	/**
	 * Set the rendered HTML of the body.
	 *
	 * For use by projections and other places that might need to set the HTML after-the-fact.
	 *
	 * @param string $html Rendered HTML.
	 * @return void
	 */
	public function setHtml(string $html): void {
		$this->rendered = $html;
	}
}
