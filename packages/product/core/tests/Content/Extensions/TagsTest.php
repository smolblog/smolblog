<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Core\Test\ContentExtensionTest;

final class TagsTest extends ContentExtensionTest {
	const string EXTENSION_KEY = 'tags';
	const string SERVICE_CLASS = TagsService::class;
	const string EXTENSION_CLASS = Tags::class;

	protected function createExampleExtension(): ContentExtension {
		return new Tags(['one', 'two', 'three', 'four']);
	}

	protected function createModifiedExtension(): ContentExtension {
		return new Tags(['can i have a little more']);
	}

	public function testAllTagsMustBeStrings() {
		$this->expectException(InvalidValueProperties::class);

		new Tags(['one', 'two', 3, 4]);
	}
}
