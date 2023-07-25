<?php

namespace Smolblog\Core\Content\Types\Note;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Test\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class NoteProjectionTest extends TestCase {
	use DatabaseTestKit;

	private NoteProjection $projection;

	public function setUp(): void {
		$this->initDatabaseWithTable('notes', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->text('markdown');
			$table->text('html');
		});

		$this->projection = new NoteProjection(db: $this->db);
	}

	public function testItWillAddANewNote() {
		$event = new NoteCreated(
			text: 'But *why?*',
			authorId: $this->randomId(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$event->setHtml('<p>But <em>why?</em></p>');

		$this->projection->onNoteCreated($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('notes'),
			content_uuid: $event->contentId->toString(),
			markdown: 'But *why?*',
			html: '<p>But <em>why?</em></p>',
		);
	}

	public function testItWillUpdateAnExistingNote() {
		$contentId = $this->randomId();
		$this->db->table('notes')->insert([
			'content_uuid' => $contentId->toString(),
			'markdown' => 'But *why?*',
			'html' => '<p>But <em>why?</em></p>',
		]);

		$event = new NoteBodyEdited(
			text: 'Seriously?',
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$event->setHtml('<p>Seriously?</p>');

		$this->projection->onNoteBodyEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('Notes'),
			content_uuid: $event->contentId->toString(),
			markdown: 'Seriously?',
			html: '<p>Seriously?</p>',
		);
	}

	public function testItWillDeleteANote() {
		$contentId = $this->randomId();
		$this->db->table('notes')->insert([
			'content_uuid' => $contentId->toString(),
			'markdown' => 'But *why?*',
			'html' => '<p>But <em>why?</em></p>',
		]);

		$event = new NoteDeleted(
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->projection->onNoteDeleted($event);

		$this->assertTableEmpty($this->db->table('notes'));
	}

	public function testItWillAddNoteDataToANoteBuilder() {
		$contentId = $this->randomId();
		$this->db->table('notes')->insert([
			'content_uuid' => $contentId->toString(),
			'markdown' => 'But *why?*',
			'html' => '<p>But <em>why?</em></p>',
		]);

		$message = $this->createMock(NoteBuilder::class);
		$message->method('getContentId')->willReturn($contentId);
		$message->expects($this->once())->method('setContentType')->with($this->equalTo(
			new Note(text: 'But *why?*', rendered: '<p>But <em>why?</em></p>')
		));

		$this->projection->buildNote($message);
	}
}
