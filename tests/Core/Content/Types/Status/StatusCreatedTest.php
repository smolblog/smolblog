<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\StatusTestKit;

include_once __DIR__ . '/_StatusTestKit.php';

final class StatusCreatedTest extends TestCase {
	use StatusTestKit;

	public function testItCreatesAStatus() {
		$status = new StatusCreated(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals(Status::class, $status->getContentType());
	}

	public function testTheTitleIsTheTextTruncated() {
		$status = new StatusCreated(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals($this->simpleTextTruncated, $status->getNewTitle());
	}

	public function testTheBodyIsTheTextFormatted() {
		$status = new StatusCreated(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
		);
		$status->setHtml($this->simpleTextFormatted);

		$this->assertEquals($this->simpleTextMd, $status->getMarkdown());
		$this->assertEquals($this->simpleTextFormatted, $status->getNewBody());
	}

	public function testItSerializesAPayloadCorrectly() {
		$expected = [
			'type' => StatusCreated::class,
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

		$actual = new StatusCreated(
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
			'type' => StatusCreated::class,
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

		$expected = new StatusCreated(
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
