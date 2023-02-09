<?php

namespace Smolblog\Core\Content\Types\Reblog;

use DateTimeImmutable;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service to handle Reblog commands.
 */
class ReblogService {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus             $bus          MessageBus for sending messages.
	 * @param ExternalContentService $embedService Service for embedding.
	 */
	public function __construct(
		private MessageBus $bus,
		private ExternalContentService $embedService,
	) {
	}

	/**
	 * Handle the CreateReblog command and create the content.
	 *
	 * @param CreateReblog $command Command to execute.
	 * @return void
	 */
	public function onCreateReblog(CreateReblog $command) {
		$info = $this->getExternalInfo($command->url);
		$reblogId = Identifier::createFromDate();

		$this->bus->dispatch(new ReblogCreated(
			url: $command->url,
			authorId: $command->userId,
			contentId: $reblogId,
			userId: $command->userId,
			siteId: $command->siteId,
			comment: $command->comment,
			info: $info,
			permalink: $reblogId->toString(),
			publishTimestamp: new DateTimeImmutable(),
			visibility: $command->publish ? ContentVisibility::Published : ContentVisibility::Draft,
		));

		$command->reblogId = $reblogId;
	}

	/**
	 * Delete a reblog.
	 *
	 * @param DeleteReblog $command Command to execute.
	 * @return void
	 */
	public function onDeleteReblog(DeleteReblog $command) {
		$this->bus->dispatch(new ReblogDeleted(
			siteId: $command->siteId,
			userId: $command->userId,
			contentId: $command->reblogId,
		));
	}

	/**
	 * Update a reblog's comment.
	 *
	 * @param EditReblogComment $command Command to execute.
	 * @return void
	 */
	public function onEditReblogComment(EditReblogComment $command) {
		$this->bus->dispatch(new ReblogCommentChanged(
			comment: $command->comment,
			contentId: $command->reblogId,
			userId: $command->userId,
			siteId: $command->siteId,
		));
	}

	/**
	 * Update a reblog's URL and info.
	 *
	 * @param EditReblogUrl $command Command to execute.
	 * @return void
	 */
	public function onEditReblogUrl(EditReblogUrl $command) {
		$info = $this->getExternalInfo($command->url);

		$this->bus->dispatch(new ReblogInfoChanged(
			url: $command->url,
			info: $info,
			contentId: $command->reblogId,
			userId: $command->userId,
			siteId: $command->siteId,
		));
	}

	/**
	 * Get the URL info using the embed service.
	 *
	 * @param string $url URL to fetch.
	 * @return ExternalContentInfo
	 */
	private function getExternalInfo(string $url): ExternalContentInfo {
		return $this->embedService->getExternalContentInfo($url);
	}
}
