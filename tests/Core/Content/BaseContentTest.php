<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\SerializableKit;

final class TestContent extends Content {
	public function getTitle(): string { return 'Title'; }
	public function getBodyContent(): string { return '<p>Hullo</p>'; }
}

final class TestContentExtension implements ContentExtension {
	use SerializableKit;
	public function __construct(public readonly string $tagline) {}
}

final class ContentTest extends TestCase {
	public function testItCanBeInstantiatedWithMinimalFields() {
		$actual = new TestContent(
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
		$actual = new TestContent(
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
			extensions: [new TestContentExtension(tagline: 'So it goes.')],
		);

		$this->assertInstanceOf(Content::class, $actual);
		$this->assertInstanceOf(TestContentExtension::class, $actual->getExtension(TestContentExtension::class));
	}

	public function testItThrowsAnErrorIfItIsPublishedWithoutAPermalink() {
		$this->expectException(InvalidContentException::class);

		new TestContent(
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);
	}

	public function testItThrowsAnErrorIfItIsPublishedWithoutATimestamp() {
		$this->expectException(InvalidContentException::class);

		new TestContent(
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/one/two.html',
			visibility: ContentVisibility::Published,
		);
	}

	public function testAnExtensionCanBeAddedAfterConstruction() {
		$actual = new TestContent(
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		);
		$actual->attachExtension(new TestContentExtension(tagline: 'hullo'));

		$this->assertInstanceOf(TestContentExtension::class, $actual->getExtension(TestContentExtension::class));
		$this->assertEquals('hullo', $actual->getExtension(TestContentExtension::class)->tagline);
	}
}
