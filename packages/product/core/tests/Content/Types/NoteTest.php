<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Core\Test\ContentTypeTest;

final class NoteTest extends ContentTypeTest {
	const string TYPE_KEY = 'note';
	const string SERVICE_CLASS = NoteService::class;
	const string TYPE_CLASS = Note::class;

	protected const CREATE_EVENT = NoteCreated::class;
	protected const UPDATE_EVENT = NoteUpdated::class;
	protected const DELETE_EVENT = NoteDeleted::class;

	protected function createExampleType(): ContentType {
		return new Note(text: new Markdown('This is _only_ a test.'));
	}

	protected function createModifiedType(): ContentType {
		return new Note(text: new Markdown('This is **only** a test.'));
	}

	public function testItCreatesTheTitleFromTruncatedTextAtFirstLineBreak() {
		$expected = "I'd just like to interject for a moment. What _you're_ refering to as Smolblog,...";
		$actual = new Note(text: new Markdown(
			"I'd just like to interject for a moment. What _you're_ refering to as Smolblog,
is in fact, WordPress/Smolblog, or as I've recently taken to calling it, WordPress plus Smolblog.
Smolblog is not a blog platform unto itself, but rather another free component of a fully
functioning WordPress system made useful by the WordPress core functionality, database
utilities and vital system components comprising a full platform as defined by bloggers
everywhere."
		));

		$this->assertEquals($expected, $actual->getTitle());
	}

	public function testItCreatesTheTileFromTruncatedTextToOneHundredCharacters() {
		$expected = "I'd just like to interject for a moment. What _you're_ refering to as Smolblog, is in fact,...";
		$actual = new Note(text: new Markdown(
			"I'd just like to interject for a moment. What _you're_ refering to as Smolblog, is in fact, WordPress/Smolblog."
		));

		$this->assertEquals($expected, $actual->getTitle());
	}

	public function testItUsesTheTextOfAShortPostAsTheTitle() {
		$expected = "I'd just like to interject for a moment.";
		$actual = new Note(text: new Markdown(
			"I'd just like to interject for a moment."
		));

		$this->assertEquals($expected, $actual->getTitle());
	}
}
