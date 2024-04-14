<?php

namespace Smolblog\Core\Federation;

use Smolblog\Core\ContentV1\Events\PublicContentAdded;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Handle Federation-related tasks.
 */
class FederationService implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus               $bus               Send messages.
	 * @param FollowerProviderRegistry $followerProviders Available follower providers.
	 */
	public function __construct(
		private MessageBus $bus,
		private FollowerProviderRegistry $followerProviders,
	) {
	}

	/**
	 * Federate content when it is published.
	 *
	 * @param PublicContentAdded $event Event to handle.
	 * @return void
	 */
	public function onPublicContentAdded(PublicContentAdded $event) {
		$content = $event->getContent();
		$siteFollowers = $this->bus->fetch(new GetFollowersForSiteByProvider($content->siteId));

		foreach ($siteFollowers as $provider => $followers) {
			$this->bus->dispatchAsync(new FederateContentToFollowers(
				content: $content,
				followers: $followers,
				provider: $provider,
			));
		}
	}

	/**
	 * Pass content and followers to the provider.
	 *
	 * @param FederateContentToFollowers $command Command to handle.
	 * @return void
	 */
	public function onFederateContentToFollowers(FederateContentToFollowers $command) {
		$provider = $this->followerProviders->get($command->provider);
		$provider->sendContentToFollowers(content: $command->content, followers: $command->followers);
	}
}
