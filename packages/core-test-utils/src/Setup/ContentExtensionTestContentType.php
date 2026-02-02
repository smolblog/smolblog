<?php

namespace Smolblog\Core\Test\Setup;

use Smolblog\Core\Content\Entities\ContentType;

final readonly class ContentExtensionTestContentType extends ContentType {
	public const KEY = 'exttest';
	public function __construct(public string $title) {}
	public function getTitle(): string {
		return $this->title;
	}
}
