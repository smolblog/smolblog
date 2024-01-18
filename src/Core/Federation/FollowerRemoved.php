<?php

namespace Smolblog\Core\Federation;

use DateTimeInterface;
use Smolblog\Core\Site\SiteEvent;
use Smolblog\Framework\Objects\Identifier;

/**
 * The blog has lost a follower, either through removal or deletion. How this looks depends on the individual module.
 */
class FollowerRemoved extends SiteEvent {
	/**
	 * Construct the event.
	 *
	 * If an action is being taken by the system and not a user, use Smolblog\Core\User\User::internalSystemUser().
	 *
	 * @param Identifier             $siteId      Site this event belongs to.
	 * @param Identifier             $userId      User instigating the event.
	 * @param string                 $provider    Service the Follower belongs to.
	 * @param string                 $providerKey ID of the Follower being removed.
	 * @param Identifier|null        $id          ID of the event.
	 * @param DateTimeInterface|null $timestamp   Time of the event.
	 */
	public function __construct(
		Identifier $siteId,
		Identifier $userId,
		public readonly string $provider,
		public readonly string $providerKey,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		parent::__construct(siteId: $siteId, userId: $userId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the payload for this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'provider' => $this->provider,
			'providerKey' => $this->providerKey,
		];
	}
}
