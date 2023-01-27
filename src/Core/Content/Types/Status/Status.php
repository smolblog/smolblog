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
	 * Markdown-formatted body text of the status.
	 *
	 * And that's it. Say what you want and get out. Maybe post a link. Maybe.
	 *
	 * @var string
	 */
	public readonly string $text;

	/**
	 * Get a title-appropriate truncation of the content.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		$truncated = substr($this->text, 0, strpos(wordwrap($this->text, 100) . "\n", "\n"));
		if (strlen($this->text) > 100) {
			$truncated .= '...';
		}
		return $truncated;
	}

	/**
	 * Get the HTML-formatted content of the status.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return $this->text;
	}

	/**
	 * Construct this content object
	 *
	 * @param string $text Markdown-formatted text of the content.
	 * @param mixed ...$props Parent properties.
	 */
	public function __construct(string $text, mixed ...$props) {
		$this->text = $text;
		//TODO: replace with actual properties.
		parent::__construct(...$props);
	}
}
