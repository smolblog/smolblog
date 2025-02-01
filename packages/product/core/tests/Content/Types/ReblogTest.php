<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Core\Test\ContentTypeTest;

final class ReblogTest extends ContentTypeTest {
	const string TYPE_KEY = 'reblog';
	const string SERVICE_CLASS = ReblogService::class;
	const string TYPE_CLASS = Reblog::class;

	protected const CREATE_EVENT = ReblogCreated::class;
	protected const UPDATE_EVENT = ReblogUpdated::class;
	protected const DELETE_EVENT = ReblogDeleted::class;

	protected function createExampleType(): ContentType {
		return new Reblog(url: new Url('https://youtu.be/AJYBaPHaNtA'), caption: new Markdown('This is _only_ a test.'));
	}

	protected function createModifiedType(): ContentType {
		return new Reblog(url: new Url('https://youtu.be/AJYBaPHaNtA'), caption: new Markdown('This is **only** a test.'));
	}

	public function testItUsesTheGivenTitle() {
		$actual = $this->createExampleType()->with(title: 'A video essay.');
		$this->assertEquals('A video essay.', $actual->getTitle());
	}

	public function testItGeneratesATitleIfNoneGiven() {
		$this->assertNotEmpty($this->createExampleType()->getTitle());
	}
}
