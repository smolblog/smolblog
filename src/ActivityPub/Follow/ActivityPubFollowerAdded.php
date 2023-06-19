<?php

namespace Smolblog\ActivityPub\Follow;

use DateTimeInterface;
use Smolblog\ActivityPhp\Type;
use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\Core\Federation\Follower;
use Smolblog\Core\Federation\FollowerAdded;
use Smolblog\Core\User\User;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicate that a new ActivityPub follower has been added.
 */
class ActivityPubFollowerAdded extends FollowerAdded {
	/**
	 * Construct the event.
	 *
	 * @param Follow                 $request   Parsed Follow activity received.
	 * @param Identifier             $siteId    Site being followed.
	 * @param Identifier|null        $userId    User making the request; default Smolbot.
	 * @param Identifier|null        $id        ID of the event.
	 * @param DateTimeInterface|null $timestamp Time of the event.
	 */
	public function __construct(
		public readonly Follow $request,
		Identifier $siteId,
		?Identifier $userId = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		parent::__construct(
			siteId: $siteId,
			userId: $userId ?? Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID),
			id: $id,
			timestamp: $timestamp,
		);
	}

	/**
	 * Get the constructed follower added by this event.
	 *
	 * @return Follower
	 */
	public function getFollower(): Follower {
		$displayName = (isset($this->request->actor->name) ? $this->request->actor->name : '') .
			'@' . $this->request->actor->preferredUsername .
			'@' . parse_url($this->request->actor->inbox, PHP_URL_HOST);

		return new Follower(
			siteId: $this->siteId,
			provider: ActivityPubFollowerProvider::SLUG,
			providerKey: $this->request->actor->id,
			displayName: $displayName,
			data: [
				'inbox' => $this->request->actor->inbox,
				'sharedInbox' => $this->request->actor->sharedInbox ?? null,
			],
		);
	}

	/**
	 * Get the payload for this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return ['request' => $this->request->toArray()];
	}

	/**
	 * Reconstruct an event from the payload.
	 *
	 * @param array $payload Serialized payload.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		return ['request' => Type::create($payload['request'])];
	}
}
