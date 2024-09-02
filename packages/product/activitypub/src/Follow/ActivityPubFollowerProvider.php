<?php

namespace Smolblog\ActivityPub\Follow;

use Exception;
use Smolblog\ActivityPub\ActivityTypesConverter;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Federation\FollowerProvider;
use Smolblog\Core\Site\GetSiteKeypair;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\User;
use Smolblog\Framework\ActivityPub\MessageSender;
use Smolblog\Framework\ActivityPub\Objects\Create;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\DateIdentifier;

/**
 * Service that handles posting content to ActivityPub.
 */
class ActivityPubFollowerProvider implements FollowerProvider {
	public const SLUG = 'activitypub';

	/**
	 * Get the slug for this provider.
	 *
	 * @return string
	 */
	public static function getSlug(): string {
		return self::SLUG;
	}

	/**
	 * Construct the service.
	 *
	 * @param MessageBus             $bus    For sending internal messages.
	 * @param ApiEnvironment         $env    For creating links.
	 * @param ActivityTypesConverter $at     For creating ActivityTypes objects.
	 * @param MessageSender          $sender For sending ActivityPub messages.
	 */
	public function __construct(
		private MessageBus $bus,
		private ApiEnvironment $env,
		private ActivityTypesConverter $at,
		private MessageSender $sender,
	) {
	}

	/**
	 * Post the given content to the given ActivityPub followers.
	 *
	 * @throws Exception When the remote server throws an error.
	 *
	 * @param Content $content   Content being created.
	 * @param array   $followers Followers interested in said content.
	 * @return void
	 */
	public function sendContentToFollowers(Content $content, array $followers): void {
		$site = $this->bus->fetch(new SiteById($content->siteId));
		$keypair = $this->bus->fetch(new GetSiteKeypair(siteId: $site->id, userId: User::internalSystemUser()->id));
		$eventId = new DateIdentifier();

		$apMessage = new Create(
			id: $this->env->getApiUrl("/site/$content->siteId/activitypub/outbox/$eventId"),
			actor: $this->env->getApiUrl("/site/$content->siteId/activitypub/actor"),
			object: $this->at->activityObjectFromContent(content: $content, site: $site),
		);

		$inboxes = array_values(array_unique(array_map(
			fn($follower) => $follower->details['sharedInbox'] ?? $follower->details['inbox'],
			$followers
		)));

		foreach ($inboxes as $inbox) {
			$this->sender->send(
				message: $apMessage,
				toInbox: $inbox,
				signedWithPrivateKey: $keypair->privateKey,
				withKeyId: "$apMessage->actor#publicKey",
			);
		}
	}
}
