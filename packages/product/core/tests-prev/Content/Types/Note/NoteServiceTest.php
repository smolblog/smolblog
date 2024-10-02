<?php

namespace Smolblog\Core\Content\Types\Note;

use DateTimeImmutable;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Test\Kits\MessageBusMockKit;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\Kits\EventComparisonTestKit;

class NoteServiceTest extends TestCase {
	use EventComparisonTestKit;
	use MessageBusMockKit;

	public function testItHasAValidConfiguration() {
		$this->assertInstanceOf(ContentTypeConfiguration::class, NoteService::getConfiguration());
	}

	public function testItHandlesTheCreateNoteCommand() {
		$command = new CreateNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			text: 'Hello, everybody!',
			publish: false,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with(
			$this->eventEquivalentTo(new NoteCreated(
				text: $command->text,
				authorId: $command->userId,
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
			))
		);

		$service = new NoteService(bus: $messageBus);
		$service->onCreateNote($command);
	}

	public function testItSendsAPublicEventIfCreateNoteSaysToPublish() {
		$command = new CreateNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			text: 'Hello, everybody!',
			publish: true,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$this->messageBusShouldDispatch($messageBus,
			$this->isInstanceOf(NoteCreated::class),
			$this->isInstanceOf(PublicNoteCreated::class),
		);

		$service = new NoteService(bus: $messageBus);
		$service->onCreateNote($command);
	}

	public function testItHandlesThePublishNoteCommand() {
		$command = new PublishNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);
		$expectedEvent = new PublicNoteCreated(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Note(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		));

		$service = new NoteService(bus: $messageBus);
		$service->onPublishNote($command);
	}

	public function testItHandlesTheEditNoteCommand() {
		$command = new EditNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			text: "What's happening?"
		);
		$expectedEvent = new NoteBodyEdited(
			text: "What's happening?",
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Note(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		));

		$service = new NoteService(bus: $messageBus);
		$service->onEditNote($command);
	}

	public function testItSendsAPublicEventWhenAPublishedNoteIsEdited() {
		$command = new EditNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			text: "What's happening?"
		);
		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$messageBus = $this->createMock(MessageBus::class);
		$this->messageBusShouldDispatch($messageBus,
			$this->eventEquivalentTo(new NoteBodyEdited(...$contentArgs, text: "What's happening?")),
			$this->eventEquivalentTo(new PublicNoteEdited(...$contentArgs)),
		);
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Note(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			visibility: ContentVisibility::Published,
			permalink: '/note/hello',
			publishTimestamp: new DateTimeImmutable(),
		));

		$service = new NoteService(bus: $messageBus);
		$service->onEditNote($command);
	}

	public function testItHandlesTheDeleteNoteCommand() {
		$command = new DeleteNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);
		$expectedEvent = new NoteDeleted(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Note(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		));

		$service = new NoteService(bus: $messageBus);
		$service->onDeleteNote($command);
	}

	public function testItSendsAPublicEventWhenAPublishedNoteIsDeleted() {
		$command = new DeleteNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);
		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$messageBus = $this->createMock(MessageBus::class);
		$this->messageBusShouldDispatch($messageBus,
			$this->eventEquivalentTo(new PublicNoteRemoved(...$contentArgs)),
			$this->eventEquivalentTo(new NoteDeleted(...$contentArgs)),
		);
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Note(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			visibility: ContentVisibility::Published,
			permalink: '/note/hello',
			publishTimestamp: new DateTimeImmutable(),
		));

		$service = new NoteService(bus: $messageBus);
		$service->onDeleteNote($command);
	}
}
