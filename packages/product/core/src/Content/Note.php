<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Type\ContentType;

readonly class Note extends ContentType {
	public const KEY = 'note';

	public function __construct(
		public Markdown $text,
	) {
	}

	public function getTitle(): string {
		return ContentUtilities::truncateText(strval($this->text));
	}
}
