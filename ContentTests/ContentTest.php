<?php

namespace Smolblog\Core\ContentV1;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Objects\SerializableKit;

final class TestContent implements ContentType {
	use SerializableKit;
	public function getTitle(): string { return 'Title'; }
	public function getBodyContent(): string { return '<p>Hullo</p>'; }
	public function getTypeKey(): string { return 'test'; }
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
			extensions: [TestContentExtension::class => new TestContentExtension(tagline: 'So it goes.')],
		);

		$this->assertInstanceOf(Content::class, $actual);
	}

	/* This test is deferred until Smolblog\Core can handle permalinks on its own. */
	public function deferred_testItThrowsAnErrorIfItIsPublishedWithoutAPermalink() {
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

	public function testItWillSerializeAndDeserializeCorrectly() {
		$object = new Content(
			type: new TestContent(),
			siteId: Identifier::fromString('9520cd54-543f-4ebb-99b9-8c06c270e545'),
			authorId: Identifier::fromString('a27b1783-f06f-49a5-af58-7b96d1275881'),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.222+00:00'),
			visibility: ContentVisibility::Published,
			id: Identifier::fromString('4fc5077b-545d-4de2-8b79-b8c4d38a5ef3'),
			extensions: [TestContentExtension::class => new TestContentExtension(tagline: 'So it goes.')],
		);

		$array = [
			'contentType' => [
				'type' => TestContent::class,
			],
			'title' => 'Title',
			'body' => '<p>Hullo</p>',
			'siteId' => '9520cd54-543f-4ebb-99b9-8c06c270e545',
			'authorId' => 'a27b1783-f06f-49a5-af58-7b96d1275881',
			'permalink' => '/test/content.html',
			'publishTimestamp' => '2022-02-22T22:22:22.222+00:00',
			'visibility' => 'published',
			'id' => '4fc5077b-545d-4de2-8b79-b8c4d38a5ef3',
			'extensions' => [
				TestContentExtension::class => ['tagline' => 'So it goes.']
			],
		];

		$this->assertEquals($object, Content::deserializeValue($array));
		$this->assertEquals($array, $object->serializeValue());
	}

	public function testItWillDeserializeWithGenericContentIfNoTypeProvided() {
		$array = [
			'title' => 'Hello',
			'body' => '<p>Goodbye</p>',
			'siteId' => '9520cd54-543f-4ebb-99b9-8c06c270e545',
			'authorId' => 'a27b1783-f06f-49a5-af58-7b96d1275881',
			'permalink' => '/test/content.html',
			'publishTimestamp' => '2022-02-22T22:22:22.222+00:00',
			'visibility' => 'published',
			'id' => '4fc5077b-545d-4de2-8b79-b8c4d38a5ef3',
			'extensions' => [
				TestContentExtension::class => ['tagline' => 'So it goes.']
			],
		];

		$object = new Content(
			type: new GenericContent(title: 'Hello', body: '<p>Goodbye</p>'),
			siteId: Identifier::fromString('9520cd54-543f-4ebb-99b9-8c06c270e545'),
			authorId: Identifier::fromString('a27b1783-f06f-49a5-af58-7b96d1275881'),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.222+00:00'),
			visibility: ContentVisibility::Published,
			id: Identifier::fromString('4fc5077b-545d-4de2-8b79-b8c4d38a5ef3'),
			extensions: [TestContentExtension::class => new TestContentExtension(tagline: 'So it goes.')],
		);

		$this->assertEquals($object, Content::deserializeValue($array));
	}
}
