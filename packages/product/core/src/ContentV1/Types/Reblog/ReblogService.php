<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use DateTimeImmutable;
use Smolblog\Core\ContentV1\ContentTypeConfiguration;
use Smolblog\Core\ContentV1\ContentTypeService;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;

/**
 * Service to handle Reblog commands.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class ReblogService implements Listener, ContentTypeService {
	/**
	 * Get the configuration for Reblogs
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			handle: 'reblog',
			displayName: 'Reblog',
			typeClass: Reblog::class,
			singleItemQuery: ReblogById::class,
			deleteItemCommand: DeleteReblog::class,
		);
	}

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

		$this->bus->dispatch(new ReblogCreated(
			url: $command->url,
			authorId: $command->userId,
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			comment: $command->comment,
			info: $info,
			publishTimestamp: new DateTimeImmutable(),
		));

		if ($command->publish) {
			$this->bus->dispatch(new PublicReblogCreated(
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
			));
		}
	}

	/**
	 * Publish a draft reblog
	 *
	 * @param PublishReblog $command Command to execute.
	 * @return void
	 */
	public function onPublishReblog(PublishReblog $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$reblog = $this->bus->fetch(new ReblogById(...$contentParams));

		if ($reblog->visibility !== ContentVisibility::Published) {
			$this->bus->dispatch(new PublicReblogCreated(...$contentParams));
		}
	}

	/**
	 * Delete a reblog.
	 *
	 * @param DeleteReblog $command Command to execute.
	 * @return void
	 */
	public function onDeleteReblog(DeleteReblog $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$reblog = $this->bus->fetch(new ReblogById(...$contentParams));

		if ($reblog->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicReblogRemoved(...$contentParams));
		}

		$this->bus->dispatch(new ReblogDeleted(...$contentParams));
	}

	/**
	 * Update a reblog's comment.
	 *
	 * @param EditReblogComment $command Command to execute.
	 * @return void
	 */
	public function onEditReblogComment(EditReblogComment $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$reblog = $this->bus->fetch(new ReblogById(...$contentParams));

		$this->bus->dispatch(new ReblogCommentChanged(
			...$contentParams,
			comment: $command->comment,
		));

		if ($reblog->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicReblogEdited(...$contentParams));
		}
	}

	/**
	 * Update a reblog's URL and info.
	 *
	 * @param EditReblogUrl $command Command to execute.
	 * @return void
	 */
	public function onEditReblogUrl(EditReblogUrl $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$reblog = $this->bus->fetch(new ReblogById(...$contentParams));
		$info = $this->getExternalInfo($command->url);

		$this->bus->dispatch(new ReblogInfoChanged(
			...$contentParams,
			url: $command->url,
			info: $info,
		));

		if ($reblog->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicReblogEdited(...$contentParams));
		}
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
