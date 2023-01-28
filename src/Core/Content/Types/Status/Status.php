<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\BaseContent;

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
	 * @param string $text     Markdown-formatted text of the content.
	 * @param mixed  ...$props Parent properties.
	 */
	public function __construct(string $text, mixed ...$props) {
		$this->internal = new InternalStatusBody(text: $text);

		// TODO: replace with actual properties.
		parent::__construct(...$props);
	}
}
