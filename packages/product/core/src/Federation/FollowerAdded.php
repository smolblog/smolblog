<?php

namespace Smolblog\Core\Federation;

use Smolblog\Core\Site\SiteEvent;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * The blog has a new follower! How this looks depends on the individual module.
 */
abstract readonly class FollowerAdded extends DomainEvent {
	/**
	 * Get the follower created by this event.
	 *
	 * @return Follower
	 */
	abstract public function getFollower(): Follower;
}
