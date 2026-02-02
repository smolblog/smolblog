<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Test\ContentExtensionTest;

#[AllowMockObjectsWithoutExpectations]
final class TagsTest extends ContentExtensionTest {
	public const string EXTENSION_KEY = 'tags';
	public const string SERVICE_CLASS = TagsService::class;
	public const string EXTENSION_CLASS = Tags::class;

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
