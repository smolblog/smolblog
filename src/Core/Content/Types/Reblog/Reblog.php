<?php

namespace Smolblog\Core\Content\Types\Reblog;

use DateTimeInterface;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Identifier;

/**
 * For reblogging interesting things from around the web.
 */
class Reblog extends Content {
	/**
	 * Undocumented function
	 *
	 * @param string                 $url              URL being reblogged.
	 * @param Identifier             $siteId           ID of the site this content belongs to.
	 * @param Identifier             $authorId         ID of the user that authored/owns this content.
	 * @param string|null            $comment          Optional markdown-formatted comment on the remote content.
	 * @param string|null            $title            The generated title, usually the remote page's title.
	 * @param string|null            $embedHtml        The HTML code to embed the Reblog's URL.
	 * @param string|null            $commentHtml      The HTML-formatted optional comment.
	 * @param string|null            $permalink        Relative URL for this content.
	 * @param DateTimeInterface|null $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility      $visibility       Visiblity of the content.
	 * @param Identifier|null        $id               ID of this content.
	 * @param array                  $extensions       Extensions attached to this content.
	 */
	public function __construct(
		public readonly string $url,
		Identifier $siteId,
		Identifier $authorId,
		public readonly ?string $comment = null,
		private ?string $title = null,
		private ?string $embedHtml = null,
		private ?string $commentHtml = null,
		?string $permalink = null,
		?DateTimeInterface $publishTimestamp = null,
		ContentVisibility $visibility = ContentVisibility::Draft,
		?Identifier $id = null,
		array $extensions = [],
	) {
		parent::__construct(
			siteId: $siteId,
			authorId: $authorId,
			permalink: $permalink,
			publishTimestamp: $publishTimestamp,
			visibility: $visibility,
			id: $id,
			extensions: $extensions ?? [],
		);
	}

	/**
	 * Get the title of this content.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * Get the rendered HTML of this content.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return $this->embedHtml . "\n\n" . $this->commentHtml;
	}

	/**
	 * Store the rendered embed code for $this->url
	 *
	 * @param string $code HTML code to embed $this->url.
	 * @return void
	 */
	public function setEmbedCode(string $code): void {
		$this->embedHtml = $code;
	}

	/**
	 * Store the rendered markdown for any additional comment.
	 *
	 * @param string $html Rendered markdown.
	 * @return void
	 */
	public function setCommentHtml(string $html): void {
		$this->commentHtml = $html;
	}

	/**
	 * Set a title for the post. Usually a variation on the external URL's title.
	 *
	 * @param string $title Title of this content.
	 * @return void
	 */
	public function setTitle(string $title): void {
		$this->title = $title;
	}
}
