<?php

namespace Smolblog\Core\Content\Types\Article;

use Cavatappi\Foundation\Fields\Markdown;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * A more longform piece of writing with a title.
 */
readonly class Article extends ContentType {
	public const KEY = 'article';

	/**
	 * Construct the Article.
	 *
	 * @param string   $title Title of the article.
	 * @param Markdown $text  Markdown-formatted text of the Article.
	 */
	public function __construct(
		public string $title,
		public Markdown $text,
	) {}

	/**
	 * Provide the title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}
}
