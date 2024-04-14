<?php

namespace Smolblog\ActivityPub\Follow;

use Exception;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Site\GetSiteKeypair;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\User;
use Smolblog\Framework\ActivityPub\MessageSender;
use Smolblog\Framework\ActivityPub\Objects\Accept;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

/**
 * Service for handling follow-related commands.
 */
class FollowService implements Listener {
	/**
	 * Create the service.
	 *
	 * @param MessageBus     $bus    For sending messages.
	 * @param ApiEnvironment $env    To assemble identifiers.
	 * @param MessageSender  $sender For sending ActivityPub requests.
	 */
	public function __construct(
		private MessageBus $bus,
		private ApiEnvironment $env,
		private MessageSender $sender,
	) {
	}

	/**
	 * Approve a follow request and send the Action back to the follower.
	 *
	 * TODO: dispatch an ActivityPubFollowerAdded event once an actual approval flow is in place.
	 *
	 * @throws Exception Thrown when sending the Accept action gives an error.
	 *
	 * @param ApproveFollowRequest $command Approval command.
	 * @return void
	 */
	public function onApproveFollowRequest(ApproveFollowRequest $command): void {
		$approvalId = new RandomIdentifier();

		$site = $command->site ?? $this->bus->fetch(new SiteById($command->siteId));
		$keypair = $this->bus->fetch(new GetSiteKeypair(siteId: $site->id, userId: User::internalSystemUser()->id));

		$body = new Accept(
			id: $this->env->getApiUrl("/site/$site->id/activitypub/outbox/$approvalId"),
			actor: $this->env->getApiUrl("/site/$site->id/activitypub/actor"),
			object: $command->request,
		);

		$this->sender->send(
			message: $body,
			toInbox: $command->actor->inbox,
			signedWithPrivateKey: $keypair->privateKey,
			withKeyId: "$body->actor#publicKey",
		);
	}

	/**
	 * Check for internal system user.
	 *
	 * @param UserCanApproveFollowers $query Security Query.
	 * @return void
	 */
	public function onUserCanApproveFollowers(UserCanApproveFollowers $query) {
		$query->setResults($query->userId->toString() == User::INTERNAL_SYSTEM_USER_ID);
	}
}
