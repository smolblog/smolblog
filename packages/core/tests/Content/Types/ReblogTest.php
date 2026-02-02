<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Fields\Markdown;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Test\ContentTypeTest;

#[AllowMockObjectsWithoutExpectations]
final class ReblogTest extends ContentTypeTest {
	public const string TYPE_KEY = 'reblog';
	public const string SERVICE_CLASS = ReblogService::class;
	public const string TYPE_CLASS = Reblog::class;

	protected const CREATE_EVENT = ReblogCreated::class;
	protected const UPDATE_EVENT = ReblogUpdated::class;
	protected const DELETE_EVENT = ReblogDeleted::class;

	protected function createExampleType(): ContentType {
		return new Reblog(
			url: HttpMessageFactory::uri('https://youtu.be/AJYBaPHaNtA'),
			caption: new Markdown('This is _only_ a test.'),
		);
	}

	protected function createModifiedType(): ContentType {
		return new Reblog(
			url: HttpMessageFactory::uri('https://youtu.be/AJYBaPHaNtA'),
			caption: new Markdown('This is **only** a test.'),
		);
	}

	public function testItUsesTheGivenTitle() {
		$actual = $this->createExampleType()->with(title: 'A video essay.');
		$this->assertEquals('A video essay.', $actual->getTitle());
	}

	public function testItGeneratesATitleIfNoneGiven() {
		$this->assertNotEmpty($this->createExampleType()->getTitle());
	}
}
