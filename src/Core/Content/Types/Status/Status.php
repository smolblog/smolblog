<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeInterface;
use Smolblog\Core\Content\BaseContent;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Identifier;

/**
 * A short, title-less post.
 *
 * While a comparison could be drawn to what the Iconfactory first called a "tweet", this is closer to a
 * Mastodon "toot" or a Micro.blog post in that it allows some basic formatting.
 */
class Status extends BaseContent {
	/**
	 * Internal body representation.
	 *
	 * @var InternalStatusBody
	 */
	private InternalStatusBody $internal;

	/**
	 * Get a title-appropriate truncation of the content.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->internal->getTruncated(100);
	}

	/**
	 * Get the HTML-formatted content of the status.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return $this->internal->text;
	}

	/**
	 * Construct this content object
	 *
	 * @param string            $text             Markdown-formatted text of the content.
	 * @param Identifier        $siteId           ID of the site this content belongs to.
	 * @param Identifier        $authorId         ID of the user that authored/owns this content.
	 * @param string            $permalink        Relative URL for this content.
	 * @param DateTimeInterface $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility $visibility       Visiblity of the content.
	 * @param Identifier|null   $id               ID of this content.
	 * @param array             $extensions       Extensions attached to this content.
	 */
	public function __construct(
		string $text,
		Identifier $siteId,
		Identifier $authorId,
		?string $permalink = null,
		?DateTimeInterface $publishTimestamp = null,
		?ContentVisibility $visibility = null,
		?Identifier $id = null,
		?array $extensions = null,
	) {
		$this->internal = new InternalStatusBody(text: $text);

		parent::__construct(
			siteId: $siteId,
			authorId: $authorId,
			permalink: $permalink,
			publishTimestamp: $publishTimestamp,
			visibility: $visibility,
			id: $id,
			extensions: $extensions,
		);
	}
}
