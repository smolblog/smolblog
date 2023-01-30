<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\StatusTestKit;

include_once __DIR__ . '/_StatusTestKit.php';

final class StatusTest extends TestCase {
	use StatusTestKit;

	public function testTheTitleIsTheTextTruncated() {
		$status = new Status(
			text: $this->simpleTextMd,
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: Identifier::createRandom(),
		);

		$this->assertEquals(
			$this->simpleTextTruncated,
			$status->getTitle()
		);
	}

	public function testTheBodyIsTheTextFormatted() {
		$status = new Status(
			text: $this->simpleTextMd,
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: Identifier::createRandom(),
		);

		$this->assertEquals(
			$this->simpleTextMd,
			$status->getBodyContent()
		);
	}
}