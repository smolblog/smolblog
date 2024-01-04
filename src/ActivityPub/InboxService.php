<?php

namespace Smolblog\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Smolblog\ActivityPub\Follow\ActivityPubFollowerAdded;
use Smolblog\ActivityPub\Follow\ApproveFollowRequest;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\User;
use Smolblog\Framework\ActivityPub\InboxAdapter;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\Infrastructure\HttpSigner;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service to handle incoming ActivityPub inbox requests.
 */
class InboxService extends InboxAdapter {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus      $bus     MessageBus for sending messages.
	 * @param ClientInterface $fetcher PSR HTTP client to use to get objects from URLs.
	 * @param HttpSigner      $signer  Service to verify signed HTTP messages.
	 * @param LoggerInterface $log     PSR logger to use.
	 */
	public function __construct(
		private MessageBus $bus,
		ClientInterface $fetcher,
		HttpSigner $signer,
		LoggerInterface $log
	) {
		parent::__construct(fetcher: $fetcher, verifier: $signer, log: $log);
	}

	/**
	 * Handle a Follow request.
	 *
	 * TODO: Save the follow request and allow a user to manually approve.
	 *
	 * @param Follow $request  Request to handle.
	 * @param mixed  $inboxKey Site being followed.
	 * @return void
	 */
	protected function handleFollow(Follow $request, mixed $inboxKey): void {
		$actor = $request->actor;
		if (is_string($request->actor)) {
			$actor = $this->getRemoteObject($request->actor);
		}

		$this->bus->dispatch(new ActivityPubFollowerAdded(
			request: $request,
			actor: $actor,
			siteId: $inboxKey,
		));

		$this->bus->dispatchAsync(new ApproveFollowRequest(
			site: $this->bus->fetch(new SiteById($inboxKey)),
			userId: Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID),
			request: $request,
			actor: $actor,
		));
	}
}
