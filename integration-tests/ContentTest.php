<?php

namespace Smolblog\Test;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\DeleteNote;
use Smolblog\Core\Content\Types\Note\EditNote;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Note\NoteById;
use Smolblog\Framework\Exceptions\MessageNotAuthorizedException;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Mock\App;
use Smolblog\Mock\MockMemoService;
use Smolblog\Mock\SecurityService;

final class ContentTest extends TestCase {
	public function testNoteLifecycle() {
		$userId = Identifier::fromString(SecurityService::SITE1AUTHOR);
		$siteId = Identifier::fromString(SecurityService::SITE1);

		$createCommand = new CreateNote(
			siteId: $siteId,
			userId: $userId,
			text: 'Hello everybody!'
		);
		App::dispatch($createCommand);

		$contentId = $createCommand->noteId;
		$content = App::fetch(new NoteById($contentId));

		$this->assertInstanceOf(Note::class, $content);
		$this->assertEquals("<p>Hello everybody!</p>\n", $content->getBodyContent());

		App::dispatch(new EditNote(
			text: 'Hello everybody! Except @oddEvan. Screw that guy.',
			noteId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::getService(MockMemoService::class)->reset();
		$this->assertEquals(
			new Note(
				text: 'Hello everybody! Except @oddEvan. Screw that guy.',
				authorId: $userId,
				id: $contentId,
				siteId: $siteId,
				publishTimestamp: $content->publishTimestamp,
				permalink: $content->permalink,
				visibility: ContentVisibility::Published,
				rendered: "<p>Hello everybody! Except @oddEvan. Screw that guy.</p>\n"
			),
			App::fetch(new NoteById($contentId))
		);

		App::dispatch(new DeleteNote(
			noteId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::getService(MockMemoService::class)->reset();
		$this->assertNull(App::fetch(new NoteById($contentId)));
	}

	public function testAuthorCanOnlyEditOwnNote() {
		$this->expectException(MessageNotAuthorizedException::class);

		$authorUserId = Identifier::fromString(SecurityService::SITE1AUTHOR);
		$adminUserId = Identifier::fromString(SecurityService::SITE1ADMIN);
		$siteId = Identifier::fromString(SecurityService::SITE1);

		$createCommand = new CreateNote(
			siteId: $siteId,
			userId: $adminUserId,
			text: 'Hello everybody!'
		);
		App::dispatch($createCommand);
		$contentId = $createCommand->noteId;

		App::dispatch(new DeleteNote(
			siteId: $siteId,
			userId: $authorUserId,
			noteId: $contentId,
		));
	}
}
