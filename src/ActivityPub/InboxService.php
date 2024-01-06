<?php

namespace Smolblog\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Smolblog\ActivityPub\Follow\ActivityPubFollowerAdded;
use Smolblog\ActivityPub\Follow\ApproveFollowRequest;
use Smolblog\Core\Federation\FollowerRemoved;
use Smolblog\Core\Federation\FollowersByProviderAndKey;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\User;
use Smolblog\Framework\ActivityPub\InboxAdapter;
use Smolblog\Framework\ActivityPub\Objects\{Actor, Delete, Follow, Undo};
use Smolblog\Framework\Infrastructure\HttpSigner;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\NamedIdentifier;

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
	 * Determine which inbox is being hit.
	 *
	 * @param ServerRequestInterface $request Incoming web request.
	 * @return mixed
	 */
	protected function determineInbox(ServerRequestInterface $request): mixed {
		$vars = $request->getAttribute('smolblogPathVars', []);
		if (isset($vars['site'])) {
			return Identifier::fromString($vars['site']);
		}
		return null;
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

	/**
	 * Handle an Undo message containing a Follow request.
	 *
	 * @param Undo   $message  Undo message received by the server.
	 * @param Follow $request  Request included in the message.
	 * @param mixed  $inboxKey Inbox request was sent to.
	 * @return void
	 */
	protected function undoFollow(Undo $message, Follow $request, mixed $inboxKey): void {
		$actorId = is_string($request->actor) ? $request->actor : $request->actor->id;
		$siteId = $this->getSiteIdFromProperty($request->object);

		$this->bus->dispatch(new FollowerRemoved(
			siteId: $siteId,
			userId: Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID),
			provider: 'activitypub',
			providerKey: new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, $actorId),
		));
	}

	/**
	 * Handle a Delete message containing an Actor.
	 *
	 * @param Delete $message  Delete message received by the server.
	 * @param Actor  $actor    Actor included in the message.
	 * @param mixed  $inboxKey Inbox message was sent to.
	 * @return void
	 */
	protected function deleteActor(Delete $message, Actor $actor, mixed $inboxKey): void {
		$actorKey = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, $actor->id);

		$followers = $this->bus->dispatch(new FollowersByProviderAndKey(
			provider: 'activitypub',
			providerKey: $actorKey,
		));

		foreach ($followers as $follower) {
			$this->bus->dispatch(new FollowerRemoved(
				siteId: $follower->siteId,
				userId: Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID),
				provider: 'activitypub',
				providerKey: $actorKey,
			));
		}
	}

	/**
	 * Turn a Smolblog actor URL or object into a site ID.
	 *
	 * @param string|Actor $prop Actor object or url string.
	 * @return Identifier
	 */
	private function getSiteIdFromProperty(string|Actor $prop): Identifier {
		$actorUrl = is_string($prop) ? $prop : $prop->id;

		$matches = [];
		$pattern = '/\/site\/([a-fA-F0-9\-]{36})\/activitypub\/actor/';
		preg_match($pattern, $actorUrl, $matches);

		return Identifier::fromString($matches[1]);
	}
}
