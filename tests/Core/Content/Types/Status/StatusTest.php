<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\StatusTestKit;

include_once __DIR__ . '/_StatusTestKit.php';

final class StatusTest extends TestCase {
	use StatusTestKit;

	public function testTheTitleIsTheTextTruncated() {
		$status = new Status(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
		);

		$this->assertEquals(
			$this->simpleTextTruncated,
			$status->getTitle()
		);
	}

	public function testTheBodyIsTheTextFormatted() {
		$status = new Status(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
		);

		$status->setHtml($this->simpleTextFormatted);

		$this->assertEquals(
			$this->simpleTextFormatted,
			$status->getBodyContent()
		);
	}

	public function testItThrowsAnExceptionIfFormattedTextIsAccessedBeforeBeingSet() {
		$this->expectException(InvalidContentException::class);

		$status = new Status(
			text: $this->simpleTextMd,
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
		);

		$status->getBodyContent();
	}
}
