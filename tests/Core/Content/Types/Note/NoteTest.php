<?php

namespace Smolblog\Core\Content\Types\Note;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\NoteTestKit;

include_once __DIR__ . '/_NoteTestKit.php';

final class NoteTest extends TestCase {
	use NoteTestKit;

	public function testTheTitleIsTheTextTruncated() {
		$note = new Note(
			text: $this->simpleTextMd,
		);

		$this->assertEquals(
			$this->simpleTextTruncated,
			$note->getTitle()
		);
	}

	public function testTheBodyIsTheTextFormatted() {
		$note = new Note(
			text: $this->simpleTextMd,
		);

		$note->setHtml($this->simpleTextFormatted);

		$this->assertEquals(
			$this->simpleTextFormatted,
			$note->getBodyContent()
		);
	}

	public function testItThrowsAnExceptionIfFormattedTextIsAccessedBeforeBeingSet() {
		$this->expectException(InvalidContentException::class);

		$note = new Note(
			text: $this->simpleTextMd,
		);

		$note->getBodyContent();
	}
}