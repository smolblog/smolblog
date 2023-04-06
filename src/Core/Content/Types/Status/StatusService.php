<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeImmutable;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service to handle Status-related commands.
 */
class StatusService implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus $bus MessageBus for sending messages.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Create a Status.
	 *
	 * @param CreateStatus $command Command information.
	 * @return void
	 */
	public function onCreateStatus(CreateStatus $command) {
		$id = Identifier::createFromDate();
		$this->bus->dispatch(new StatusCreated(
			text: $command->text,
			authorId: $command->userId,
			contentId: $id,
			userId: $command->userId,
			siteId: $command->siteId,
			permalink: '/status/' . $id->toString(),
			publishTimestamp: new DateTimeImmutable(),
			visibility: $command->publish ? ContentVisibility::Published : ContentVisibility::Draft,
		));

		$command->statusId = $id;
	}

	/**
	 * Edit a status
	 *
	 * @param EditStatus $command Command information.
	 * @return void
	 */
	public function onEditStatus(EditStatus $command) {
		$this->bus->dispatch(new StatusBodyEdited(
			text: $command->text,
			contentId: $command->statusId,
			userId: $command->userId,
			siteId: $command->siteId,
		));
	}

	/**
	 * Delete a status
	 *
	 * @param DeleteStatus $command Command information.
	 * @return void
	 */
	public function onDeleteStatus(DeleteStatus $command) {
		$this->bus->dispatch(new StatusDeleted(
			contentId: $command->statusId,
			userId: $command->userId,
			siteId: $command->siteId,
		));
	}
}
