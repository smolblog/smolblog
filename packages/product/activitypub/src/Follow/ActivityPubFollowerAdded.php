<?php

namespace Smolblog\ActivityPub\Follow;

use DateTimeInterface;
use Smolblog\Core\Federation\Follower;
use Smolblog\Core\Federation\FollowerAdded;
use Smolblog\Core\User\User;
use Smolblog\Framework\ActivityPub\Objects\Actor;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;

/**
 * Indicate that a new ActivityPub follower has been added.
 */
class ActivityPubFollowerAdded extends FollowerAdded {
	/**
	 * Construct the event.
	 *
	 * @param Follow                 $request   Parsed Follow activity received.
	 * @param Actor                  $actor     Actor giving the request.
	 * @param Identifier             $siteId    Site being followed.
	 * @param Identifier|null        $userId    User making the request; default Smolbot.
	 * @param Identifier|null        $id        ID of the event.
	 * @param DateTimeInterface|null $timestamp Time of the event.
	 */
	public function __construct(
		public readonly Follow $request,
		public readonly Actor $actor,
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
		$displayName = (isset($this->actor->name) ? $this->actor->name : '') .
			' (@' . $this->actor->preferredUsername .
			'@' . parse_url($this->actor->inbox, PHP_URL_HOST) . ')';

		return new Follower(
			siteId: $this->siteId,
			provider: ActivityPubFollowerProvider::SLUG,
			providerKey: new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, $this->actor->id),
			displayName: $displayName,
			details: [
				'actor' => $this->actor->id,
				'inbox' => $this->actor->inbox,
				'sharedInbox' =>
					isset($this->actor->endpoints) ? ($this->actor->endpoints['sharedInbox'] ?? null) : null,
			],
		);
	}

	/**
	 * Get the payload for this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return ['request' => $this->request->toArray(), 'actor' => $this->actor->toArray()];
	}

	/**
	 * Reconstruct an event from the payload.
	 *
	 * @param array $payload Serialized payload.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		return ['request' => Follow::fromArray($payload['request']), 'actor' => Actor::fromArray($payload['actor'])];
	}
}
