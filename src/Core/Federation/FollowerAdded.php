<?php

namespace Smolblog\Core\Federation;

use Smolblog\Core\Site\SiteEvent;

/**
 * The blog has a new follower! How this looks depends on the individual module.
 */
abstract class FollowerAdded extends SiteEvent {
	/**
	 * Get the follower created by this event.
	 *
	 * @return Follower
	 */
	abstract public function getFollower(): Follower;
}
