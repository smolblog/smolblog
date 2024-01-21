<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Test\TestCase;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Test\Kits\NoteTestKit;

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

	public function testItsTypeKeyIsNote() {
		$this->assertEquals('note', (new Note('a'))->getTypeKey());
	}
}
