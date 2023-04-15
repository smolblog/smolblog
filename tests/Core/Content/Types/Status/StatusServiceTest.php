<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
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

		$service = new StatusService(bus: $messageBus);
		$service->onDeleteStatus($command);
	}
}
