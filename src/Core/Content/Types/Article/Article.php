<?php

namespace Smolblog\Core\Content\Types\Article;

use Smolblog\Core\Content\Fields\Markdown;

class Article {
	public function __construct(
		public readonly string $title,
		public readonly Markdown $body,
	) {
	}
}
