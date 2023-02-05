<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\StatusTestKit;

include_once __DIR__ . '/_StatusTestKit.php';

final class StatusBodyEditedTest extends TestCase {
	use StatusTestKit;

	public function testTheTitleIsTheTextTruncated() {
		$status = new StatusBodyEdited(
			text: $this->simpleTextMd,
			siteId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
		);

		$this->assertEquals($this->simpleTextTruncated, $status->getNewTitle());
	}

	public function testTheBodyIsTheTextFormatted() {
		$status = new StatusBodyEdited(
			text: $this->simpleTextMd,
			siteId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
		);
		$status->setHtml($this->simpleTextFormatted);

		$this->assertEquals($this->simpleTextMd, $status->getMarkdown());
		$this->assertEquals($this->simpleTextFormatted, $status->getNewBody());
	}

	public function testItSerializesAPayloadCorrectly() {
		$expected = [
			'type' => StatusBodyEdited::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'text' => 'There\'s a horse loose in a hospital!'
			]
		];

		$actual = new StatusBodyEdited(
			text: 'There\'s a horse loose in a hospital!',
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals($expected, $actual->toArray());
	}
}
