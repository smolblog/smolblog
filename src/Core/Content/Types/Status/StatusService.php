<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeImmutable;
use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentTypeService;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service to handle Status-related commands.
 */
class StatusService implements Listener, ContentTypeService {
	/**
	 * Get the Status configuration.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			handle: 'status',
			displayName: 'Status',
			typeClass: Status::class,
			singleItemQuery: StatusById::class,
		);
	}

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
		$id = new DateIdentifier();
		$this->bus->dispatch(new StatusCreated(
			text: $command->text,
			authorId: $command->userId,
			contentId: $id,
			userId: $command->userId,
			siteId: $command->siteId,
			publishTimestamp: new DateTimeImmutable(),
		));

		if ($command->publish) {
			$this->bus->dispatch(new PublicStatusCreated(
				contentId: $id,
				userId: $command->userId,
				siteId: $command->siteId,
			));
		}

		$command->statusId = $id;
	}

	/**
	 * Edit a status
	 *
	 * @param EditStatus $command Command information.
	 * @return void
	 */
	public function onEditStatus(EditStatus $command) {
		$contentParams = [
			'contentId' => $command->statusId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$status = $this->bus->fetch(new StatusById(...$contentParams));

		$this->bus->dispatch(new StatusBodyEdited(
			...$contentParams,
			text: $command->text,
		));

		if ($status->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicStatusEdited(...$contentParams));
		}
	}

	/**
	 * Publish a draft status
	 *
	 * @param PublishStatus $command Command to execute.
	 * @return void
	 */
	public function onPublishStatus(PublishStatus $command) {
		$contentParams = [
			'contentId' => $command->statusId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$status = $this->bus->fetch(new StatusById(...$contentParams));

		if ($status->visibility !== ContentVisibility::Published) {
			$this->bus->dispatch(new PublicStatusCreated(...$contentParams));
		}
	}

	/**
	 * Delete a status
	 *
	 * @param DeleteStatus $command Command information.
	 * @return void
	 */
	public function onDeleteStatus(DeleteStatus $command) {
		$contentParams = [
			'contentId' => $command->statusId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$status = $this->bus->fetch(new StatusById(...$contentParams));

		if ($status->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicStatusRemoved(...$contentParams));
		}

		$this->bus->dispatch(new StatusDeleted(...$contentParams));
	}
}
