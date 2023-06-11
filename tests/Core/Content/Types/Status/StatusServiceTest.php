<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeImmutable;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\EventComparisonTestKit;

class StatusServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheCreateStatusCommand() {
		$command = new CreateStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			text: 'Hello, everybody!',
			publish: false,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(StatusCreated::class));

		$service = new StatusService(bus: $messageBus);
		$service->onCreateStatus($command);
	}

	public function testItSendsAPublicEventIfCreateStatusSaysToPublish() {
		$command = new CreateStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			text: 'Hello, everybody!',
			publish: true,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->isInstanceOf(StatusCreated::class)],
			[$this->isInstanceOf(PublicStatusCreated::class)],
		);

		$service = new StatusService(bus: $messageBus);
		$service->onCreateStatus($command);
	}

	public function testItHandlesThePublishStatusCommand() {
		$command = new PublishStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			statusId: $this->randomId(),
		);
		$expectedEvent = new PublicStatusCreated(
			contentId: $command->statusId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Status(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		));

		$service = new StatusService(bus: $messageBus);
		$service->onPublishStatus($command);
	}

	public function testItHandlesTheEditStatusCommand() {
		$command = new EditStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			statusId: $this->randomId(),
			text: "What's happening?"
		);
		$expectedEvent = new StatusBodyEdited(
			text: "What's happening?",
			contentId: $command->statusId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Status(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		));

		$service = new StatusService(bus: $messageBus);
		$service->onEditStatus($command);
	}

	public function testItSendsAPublicEventWhenAPublishedStatusIsEdited() {
		$command = new EditStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			statusId: $this->randomId(),
			text: "What's happening?"
		);
		$contentArgs = [
			'contentId' => $command->statusId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new StatusBodyEdited(...$contentArgs, text: "What's happening?"))],
			[$this->eventEquivalentTo(new PublicStatusEdited(...$contentArgs))],
		);
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Status(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			visibility: ContentVisibility::Published,
			permalink: '/status/hello',
			publishTimestamp: new DateTimeImmutable(),
		));

		$service = new StatusService(bus: $messageBus);
		$service->onEditStatus($command);
	}

	public function testItHandlesTheDeleteStatusCommand() {
		$command = new DeleteStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			statusId: $this->randomId(),
		);
		$expectedEvent = new StatusDeleted(
			contentId: $command->statusId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Status(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		));

		$service = new StatusService(bus: $messageBus);
		$service->onDeleteStatus($command);
	}

	public function testItSendsAPublicEventWhenAPublishedStatusIsDeleted() {
		$command = new DeleteStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			statusId: $this->randomId(),
		);
		$contentArgs = [
			'contentId' => $command->statusId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new PublicStatusRemoved(...$contentArgs))],
			[$this->eventEquivalentTo(new StatusDeleted(...$contentArgs))],
		);
		$messageBus->method('fetch')->willReturn(new Content(
			type: new Status(text: 'Hello'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			visibility: ContentVisibility::Published,
			permalink: '/status/hello',
			publishTimestamp: new DateTimeImmutable(),
		));

		$service = new StatusService(bus: $messageBus);
		$service->onDeleteStatus($command);
	}
}
