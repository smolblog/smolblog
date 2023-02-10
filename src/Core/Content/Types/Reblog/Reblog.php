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
	 * @param string                   $url              URL being reblogged.
	 * @param Identifier               $siteId           ID of the site this content belongs to.
	 * @param Identifier               $authorId         ID of the user that authored/owns this content.
	 * @param string|null              $comment          Optional markdown-formatted comment on the remote content.
	 * @param ExternalContentInfo|null $info             Fetched info from the external URL.
	 * @param string|null              $commentHtml      The HTML-formatted optional comment.
	 * @param string|null              $permalink        Relative URL for this content.
	 * @param DateTimeInterface|null   $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility        $visibility       Visiblity of the content.
	 * @param Identifier|null          $id               ID of this content.
	 * @param array                    $extensions       Extensions attached to this content.
	 */
	public function __construct(
		public readonly string $url,
		Identifier $siteId,
		Identifier $authorId,
		public readonly ?string $comment = null,
		private ?ExternalContentInfo $info = null,
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
		return $this->info->title;
	}

	/**
	 * Get the rendered HTML of this content.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return $this->info->embed . "\n\n" . $this->commentHtml;
	}

	/**
	 * Set the info for the external URL
	 *
	 * @param ExternalContentInfo $info Fetched info.
	 * @return void
	 */
	public function setExternalInfo(ExternalContentInfo $info): void {
		$this->info = $info;
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
}
