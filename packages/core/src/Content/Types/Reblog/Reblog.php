<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Cavatappi\Foundation\Fields\Markdown;
use Psr\Http\Message\UriInterface;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * An embedded post from another site, such as YouTube or Tumblr.
 */
readonly class Reblog extends ContentType {
	public const KEY = 'reblog';

	/**
	 * Construct the Reblog.
	 *
	 * @param UriInterface  $url     URL being reblogged.
	 * @param string|null   $title   Optional title for the content.
	 * @param Markdown|null $caption Optional caption or comment on the reblogged post.
	 */
	public function __construct(
		public UriInterface $url,
		public ?string $title = null,
		public ?Markdown $caption = null,
	) {}

	/**
	 * Construct the title from the URL if no title is given.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title ?? 'Reblog from ' . $this->url->getHost();
	}
}
