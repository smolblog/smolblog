<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeInterface;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Framework\Objects\Identifier;

/**
 * A short, title-less post.
 *
 * While a comparison could be drawn to what the Iconfactory first called a "tweet", this is closer to a
 * Mastodon "toot" or a Micro.blog post in that it allows some basic formatting.
 */
class Status extends Content {
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
	 * Get the HTML-formatted content of the status.
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
	 * Construct this content object
	 *
	 * @param string            $text             Markdown-formatted text of the content.
	 * @param Identifier        $siteId           ID of the site this content belongs to.
	 * @param Identifier        $authorId         ID of the user that authored/owns this content.
	 * @param DateTimeInterface $publishTimestamp Date and time this content was first published.
	 * @param string|null       $permalink        Relative URL for this content.
	 * @param ContentVisibility $visibility       Visiblity of the content.
	 * @param Identifier|null   $id               ID of this content.
	 * @param array             $extensions       Extensions attached to this content.
	 * @param string|null       $rendered         Rendered HTML of the content.
	 */
	public function __construct(
		public readonly string $text,
		Identifier $siteId,
		Identifier $authorId,
		?DateTimeInterface $publishTimestamp = null,
		?string $permalink = null,
		?ContentVisibility $visibility = ContentVisibility::Draft,
		?Identifier $id = null,
		?array $extensions = null,
		private ?string $rendered = null,
	) {
		parent::__construct(
			siteId: $siteId,
			authorId: $authorId,
			publishTimestamp: $publishTimestamp,
			permalink: $permalink,
			visibility: $visibility,
			id: $id,
			extensions: $extensions ?? [],
		);
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
