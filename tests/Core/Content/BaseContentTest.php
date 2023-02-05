<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
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
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
		);

		$this->assertInstanceOf(Content::class, $actual);
		$this->assertEquals(ContentVisibility::Draft, $actual->visibility);
		$this->assertNull($actual->publishTimestamp);
		$this->assertNull($actual->permalink);
		$this->assertInstanceOf(Identifier::class, $actual->id);
	}

	public function testItCanBeCreatedWithAllFields() {
		$actual = new TestContent(
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: Identifier::createRandom(),
			extensions: [new TestContentExtension(tagline: 'So it goes.')],
		);

		$this->assertInstanceOf(Content::class, $actual);
		$this->assertInstanceOf(TestContentExtension::class, $actual->getExtension(TestContentExtension::class));
	}

	public function testItThrowsAnErrorIfItIsPublishedWithoutAPermalink() {
		$this->expectException(InvalidContentException::class);

		new TestContent(
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);
	}

	public function testItThrowsAnErrorIfItIsPublishedWithoutATimestamp() {
		$this->expectException(InvalidContentException::class);

		new TestContent(
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
			permalink: '/one/two.html',
			visibility: ContentVisibility::Published,
		);
	}

	public function testAnExtensionCanBeAddedAfterConstruction() {
		$actual = new TestContent(
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
		);
		$actual->attachExtension(new TestContentExtension(tagline: 'hullo'));

		$this->assertInstanceOf(TestContentExtension::class, $actual->getExtension(TestContentExtension::class));
		$this->assertEquals('hullo', $actual->getExtension(TestContentExtension::class)->tagline);
	}
}
