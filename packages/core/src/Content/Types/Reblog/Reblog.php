<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Foundation\Value\ValueKit;
use Psr\Http\Message\UriInterface;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * An embedded post from another site, such as YouTube or Tumblr.
 */
readonly class Reblog implements ContentType {
	use ValueKit;

	public static function getKey(): string {
		return 'reblog';
	}

	/**
	 * Title of the content.
	 *
	 * @var string
	 */
	public string $title;

	/**
	 * Construct the Reblog.
	 *
	 * @param UriInterface  $url     URL being reblogged.
	 * @param string|null   $title   Optional title for the content.
	 * @param Markdown|null $caption Optional caption or comment on the reblogged post.
	 */
	public function __construct(
		public UriInterface $url,
		?string $title = null,
		public ?Markdown $caption = null,
	) {
		$this->title = $title ?? 'Reblog from ' . $this->url->getHost();
	}
}
