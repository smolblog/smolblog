<?php

namespace Smolblog\Core;

use Smolblog\Core\Content\Type\ContentType;
use Smolblog\Test\TestCase;

final readonly class ContentTypeStub extends ContentType {
	public const KEY = 'test';
	public function __construct(public string $text = 'title') {}
	public function getTitle(): string { return $this->text; }
}

final class ContentTest extends TestCase {
	public function testItCanBeCreated() {
		$type = new ContentTypeStub();

		$object = new Content(
			body: $type,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		);

		$this->assertInstanceOf(Content::class, $object);
		$this->assertEquals('title', $object->title());
		$this->assertEquals('test', $object->type());
	}
}
