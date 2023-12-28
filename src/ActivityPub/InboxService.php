<?php

namespace Smolblog\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Smolblog\ActivityPhp\Type;
use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\ActivityPub\Follow\ActivityPubFollowerAdded;
use Smolblog\ActivityPub\Follow\ApproveFollowRequest;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\User;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service to handle incoming ActivityPub inbox requests.
 */
class InboxService {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus      $bus     MessageBus to dispatch events and queries.
	 * @param ClientInterface $fetcher Get full objects from provided URLs.
	 */
	public function __construct(
		private MessageBus $bus,
		private ClientInterface $fetcher,
	) {
	}

	/**
	 * Handle a Follow request.
	 *
	 * TODO: Save the follow request and allow a user to manually approve.
	 *
	 * @param Follow     $request Request to handle.
	 * @param Identifier $siteId  Site being followed.
	 * @return void
	 */
	public function handleFollow(Follow $request, Identifier $siteId): void {
		$actor = $request->actor;
		if (is_string($request->actor)) {
			$actorResponse = $this->fetcher->sendRequest(new HttpRequest(
				verb: HttpVerb::GET,
				url: $request->actor,
				headers: ['accept' => 'application/json'],
			));

			$actor = Type::fromJson($actorResponse->getBody()->getContents());
		}

		$this->bus->dispatch(new ActivityPubFollowerAdded(
			request: $request,
			actor: $actor,
			siteId: $siteId,
		));

		$this->bus->dispatchAsync(new ApproveFollowRequest(
			site: $this->bus->fetch(new SiteById($siteId)),
			userId: Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID),
			request: $request,
			actor: $actor,
		));
	}
}

/*
	Example follow:

	{
		"@context": "https://www.w3.org/ns/activitystreams",
		"type": "Follow",
		"id": "https://opalstack.social/8acacafd-b896-4377-8ab5-b0a556f11c25",
		"actor": "https://opalstack.social/users/smolblog",
		"object": "https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/actor"
	}
*/
