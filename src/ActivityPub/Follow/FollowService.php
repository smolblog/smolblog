<?php

namespace Smolblog\ActivityPub\Follow;

use Psr\Http\Client\ClientInterface;
use Smolblog\ActivityPhp\Type\Extended\Activity\Accept;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\Infrastructure\HttpSigner;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Framework\Objects\RandomIdentifier;

/**
 * Service for handling follow-related commands.
 */
class FollowService implements Listener {
	/**
	 * Create the service.
	 *
	 * @param MessageBus      $bus     For sending messages.
	 * @param ClientInterface $fetcher HTTP client for sending the approval.
	 * @param HttpSigner      $signer  For signing HTTP requests.
	 * @param ApiEnvironment  $env     To assemble identifiers.
	 */
	public function __construct(
		private MessageBus $bus,
		private ClientInterface $fetcher,
		private HttpSigner $signer,
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Approve a follow request and send the Action back to the follower.
	 *
	 * TODO: dispatch an ActivityPubFollowerAdded event once an actual approval flow is in place.
	 *
	 * @param ApproveFollowRequest $command Approval command.
	 * @return void
	 */
	public function onApproveFollowRequest(ApproveFollowRequest $command): void {
		$approvalId = new RandomIdentifier();

		$site = $command->site ?? $this->bus->fetch(new SiteById($command->siteId));

		$body = new Accept();
		$body->id = $this->env->getApiUrl("/site/$site->id/activitypub/outbox/$approvalId");
		$body->actor = $this->env->getApiUrl("/site/$site->id/activitypub/actor");
		$body->object = $command->request;

		$this->fetcher->sendRequest($this->signer->sign(
			request: new HttpRequest(
				verb: HttpVerb::POST,
				url: $command->request->actor->inbox,
				body: $body->toArray(),
			),
			keyId: "$body->actor#publicKey",
			keyPem: $site->publicKey,
		));
	}
}
