<?php

namespace Smolblog\Core\Content\Types\Status;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

class StatusServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheCreateStatusCommand() {
		$command = new CreateStatus(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			text: 'Hello, everybody!',
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(StatusCreated::class));

		$service = new StatusService(bus: $messageBus);
		$service->onCreateStatus($command);
	}

	public function testItHandlesTheEditStatusCommand() {
		$command = new EditStatus(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			statusId: Identifier::createRandom(),
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

		$service = new StatusService(bus: $messageBus);
		$service->onEditStatus($command);
	}

	public function testItHandlesTheDeleteStatusCommand() {
		$command = new DeleteStatus(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			statusId: Identifier::createRandom(),
		);
		$expectedEvent = new StatusDeleted(
			contentId: $command->statusId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new StatusService(bus: $messageBus);
		$service->onDeleteStatus($command);
	}
}
