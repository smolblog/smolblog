<?php

namespace Smolblog\Core\ContentV1\Extensions\Tags;

use Smolblog\Core\ContentV1\ContentExtensionConfiguration;
use Smolblog\Core\ContentV1\ContentExtensionService;
use Smolblog\Core\ContentV1\ContentUtilityKit;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Handle Tag commands.
 */
class TagService implements Listener, ContentExtensionService {
	use ContentUtilityKit;

	/**
	 * Get the extension configuration.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration {
		return new ContentExtensionConfiguration(
			handle: 'tags',
			displayName: 'Tags',
			extensionClass: Tags::class,
		);
	}

	/**
	 * Construct the service.
	 *
	 * @param MessageBus $bus MessageBus instance for sending events.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Handle the SetTags command.
	 *
	 * @param SetTags $command Command to set tags on a piece of content.
	 * @return void
	 */
	public function onSetTags(SetTags $command): void {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->dispatch(new TagsSet(
			...$contentParams,
			tagText: $command->tags,
		));

		$this->dispatchIfContentPublic(
			message: new PublicContentTagsChanged(...$contentParams),
			contentParams: $contentParams,
		);
	}
}
