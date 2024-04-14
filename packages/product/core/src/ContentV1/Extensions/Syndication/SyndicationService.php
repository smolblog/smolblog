<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Smolblog\Core\ContentV1\ContentExtensionConfiguration;
use Smolblog\Core\ContentV1\ContentExtensionService;
use Smolblog\Core\ContentV1\ContentUtilityKit;
use Smolblog\Core\ContentV1\ContentUtilityService;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Handle Syndication commands.
 */
class SyndicationService implements Listener, ContentExtensionService {
	use ContentUtilityKit;

	/**
	 * Get the extension configuration.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration {
		return new ContentExtensionConfiguration(
			handle: 'syndication',
			displayName: 'Syndication',
			extensionClass: Syndication::class,
		);
	}

	/**
	 * Construct the service
	 *
	 * @param MessageBus $bus For dispatching Events.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Handle the AddSyndicationLink command.
	 *
	 * @param AddSyndicationLink $command Command to execute.
	 * @return void
	 */
	public function onAddSyndicationLink(AddSyndicationLink $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->dispatch(new ContentSyndicated(
			...$contentParams,
			url: $command->url,
		));

		$this->dispatchIfContentPublic(
			message: new PublicContentSyndicationChanged(...$contentParams),
			contentParams: $contentParams
		);
	}

	/**
	 * Handle the SetSyndicationChannels command
	 *
	 * @param SetSyndicationChannels $command Command to execute.
	 * @return void
	 */
	public function onSetSyndicationChannels(SetSyndicationChannels $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->dispatch(new SyndicationChannelsSet(
			...$contentParams,
			channels: $command->channels,
		));

		$this->dispatchIfContentPublic(
			message: new PublicContentSyndicationChanged(...$contentParams),
			contentParams: $contentParams
		);
	}
}
