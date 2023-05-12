<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\SerializableKit;

final class TestContent implements ContentType {
	public function getTitle(): string { return 'Title'; }
	public function getBodyContent(): string { return '<p>Hullo</p>'; }
}

final class TestContentExtension implements ContentExtension {
	use SerializableKit;
	public function __construct(public readonly string $tagline) {}
}

final class ContentTest extends TestCase {
	public function testItCanBeInstantiatedWithMinimalFields() {
		$actual = new Content(
			type: new TestContent(),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		);

		$this->assertInstanceOf(Content::class, $actual);
		$this->assertEquals(ContentVisibility::Draft, $actual->visibility);
		$this->assertNull($actual->publishTimestamp);
		$this->assertNull($actual->permalink);
		$this->assertInstanceOf(Identifier::class, $actual->id);
	}

	public function testItCanBeCreatedWithAllFields() {
		$actual = new Content(
			type: new TestContent(),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
			extensions: [new TestContentExtension(tagline: 'So it goes.')],
		);

		$this->assertInstanceOf(Content::class, $actual);
	}

	public function testItThrowsAnErrorIfItIsPublishedWithoutAPermalink() {
		$this->expectException(InvalidContentException::class);

		new Content(
			type: new TestContent(),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);
	}

	public function testItThrowsAnErrorIfItIsPublishedWithoutATimestamp() {
		$this->expectException(InvalidContentException::class);

		new Content(
			type: new TestContent(),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/one/two.html',
			visibility: ContentVisibility::Published,
		);
	}
}
