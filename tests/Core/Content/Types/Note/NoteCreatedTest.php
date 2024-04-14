<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\NoteTestKit;

final class NoteCreatedTest extends TestCase {
	use NoteTestKit;

	public function testItCreatesANote() {
		$note = new NoteCreated(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals('note', $note->getContentType());
	}

	public function testTheTitleIsTheTextTruncated() {
		$note = new NoteCreated(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals($this->simpleTextTruncated, $note->getNewTitle());
	}

	public function testTheBodyIsTheTextFormatted() {
		$note = new NoteCreated(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
		);
		$note->setMarkdownHtml([$this->simpleTextFormatted]);

		$this->assertEquals([$this->simpleTextMd], $note->getMarkdown());
		$this->assertEquals($this->simpleTextFormatted, $note->getNewBody());
	}

	public function testItSerializesAPayloadCorrectly() {
		$expected = [
			'type' => NoteCreated::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'authorId' => '376ee1ba-4544-4e9e-827f-2792b0c67c76',
				'text' => 'There\'s a horse loose in a hospital!'
			]
		];

		$actual = new NoteCreated(
			text: 'There\'s a horse loose in a hospital!',
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			authorId: Identifier::fromString('376ee1ba-4544-4e9e-827f-2792b0c67c76'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals($expected, $actual->toArray());
	}

	public function testItDeserializesAPayloadCorrectly() {
		$actual = [
			'type' => NoteCreated::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'authorId' => '376ee1ba-4544-4e9e-827f-2792b0c67c76',
				'text' => 'There\'s a horse loose in a hospital!'
			]
		];

		$expected = new NoteCreated(
			text: 'There\'s a horse loose in a hospital!',
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			authorId: Identifier::fromString('376ee1ba-4544-4e9e-827f-2792b0c67c76'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals($expected, ContentEvent::fromTypedArray($actual));
	}
}
