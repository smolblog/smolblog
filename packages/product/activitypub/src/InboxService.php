<?php

namespace Smolblog\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Smolblog\ActivityPub\Follow\ActivityPubFollowerAdded;
use Smolblog\ActivityPub\Follow\ApproveFollowRequest;
use Smolblog\Core\Federation\FollowerRemoved;
use Smolblog\Core\Federation\FollowersByProviderAndKey;
use Smolblog\Core\Site\GetSiteKeypair;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\User;
use Smolblog\Framework\ActivityPub\InboxAdapter;
use Smolblog\Framework\ActivityPub\InboxRequestContext;
use Smolblog\Framework\ActivityPub\ObjectGetter;
use Smolblog\Framework\ActivityPub\Objects\{Actor, Delete, Follow, Undo};
use Smolblog\Framework\ActivityPub\Signatures\MessageSigner;
use Smolblog\Framework\ActivityPub\Signatures\MessageVerifier;
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
	 * @param MessageBus             $bus    MessageBus for sending messages.
	 * @param ActivityTypesConverter $at     For creating ActivityTypes objects.
	 * @param ObjectGetter|null      $getter Optional ObjectGetter for getting remote objects.
	 * @param MessageVerifier        $signer Service to verify signed HTTP messages.
	 * @param LoggerInterface        $log    PSR logger to use.
	 */
	public function __construct(
		private MessageBus $bus,
		private ActivityTypesConverter $at,
		ObjectGetter $getter,
		MessageVerifier $signer,
		LoggerInterface $log,
	) {
		parent::__construct(getter: $getter, verifier: $signer, log: $log);
	}

	/**
	 * Determine which inbox is being hit.
	 *
	 * @param ServerRequestInterface $request Incoming web request.
	 * @return InboxRequestContext
	 */
	protected function determineInbox(ServerRequestInterface $request): InboxRequestContext {
		$vars = $request->getAttribute('smolblogPathVars', []);
		if (!isset($vars['site'])) {
			return new InboxRequestContext(inboxKey: null);
		}

		$siteId = Identifier::fromString($vars['site']);
		$site = $this->bus->fetch(new SiteById($siteId));
		$keypair = $this->bus->fetch(new GetSiteKeypair(siteId: $siteId, userId: User::internalSystemUser()->id));

		return new InboxRequestContext(
			inboxKey: $siteId,
			inboxActor: $this->at->actorFromSite($site),
			privateKeyPem: $keypair->privateKey,
		);
	}

	/**
	 * Handle a Follow request.
	 *
	 * TODO: Save the follow request and allow a user to manually approve.
	 *
	 * @param Follow              $request      Request to handle.
	 * @param InboxRequestContext $inboxContext Site being followed.
	 * @return void
	 */
	protected function handleFollow(Follow $request, InboxRequestContext $inboxContext): void {
		$actor = $request->actor;
		if (is_string($request->actor)) {
			$actor = $this->getter->get(
				url: $request->actor,
				signedWithPrivateKey: $inboxContext->privateKeyPem,
				withKeyId: $inboxContext->inboxActor?->publicKey->id,
			);
		}

		$this->bus->dispatch(new ActivityPubFollowerAdded(
			request: $request,
			actor: $actor,
			siteId: $inboxContext->inboxKey,
		));

		$this->bus->dispatchAsync(new ApproveFollowRequest(
			site: $this->bus->fetch(new SiteById($inboxContext->inboxKey)),
			userId: Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID),
			request: $request,
			actor: $actor,
		));
	}

	/**
	 * Handle an Undo message containing a Follow request.
	 *
	 * @param Undo                $message      Undo message received by the server.
	 * @param Follow              $request      Request included in the message.
	 * @param InboxRequestContext $inboxContext Inbox request was sent to.
	 * @return void
	 */
	protected function undoFollow(Undo $message, Follow $request, InboxRequestContext $inboxContext): void {
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
	 * @param Delete              $message      Delete message received by the server.
	 * @param Actor               $actor        Actor included in the message.
	 * @param InboxRequestContext $inboxContext Inbox message was sent to.
	 * @return void
	 */
	protected function deleteActor(Delete $message, Actor $actor, InboxRequestContext $inboxContext): void {
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
