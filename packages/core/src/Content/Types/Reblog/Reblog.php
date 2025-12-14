<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;

/**
 * An embedded post from another site, such as YouTube or Tumblr.
 */
readonly class Reblog extends ContentType {
	public const KEY = 'reblog';

	/**
	 * Construct the Reblog.
	 *
	 * @param Url           $url     URL being reblogged.
	 * @param string|null   $title   Optional title for the content.
	 * @param Markdown|null $caption Optional caption or comment on the reblogged post.
	 */
	public function __construct(
		public Url $url,
		public ?string $title = null,
		public ?Markdown $caption = null,
	) {
	}

	/**
	 * Construct the title from the URL if no title is given.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title ?? 'Reblog from ' . $this->url->getHost();
	}
}
