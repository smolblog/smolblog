<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Type\ContentType;
use Smolblog\Foundation\Value\Fields\Url;

readonly class Reblog extends ContentType {
	public const KEY = 'reblog';

	public function __construct(
		public Url $url,
		public ?string $title = null,
		public ?Markdown $caption = null,
	) {
	}

	public function getTitle(): string {
		return $this->title ?? 'Reblog from ' . $this->url->getHost();
	}
}
