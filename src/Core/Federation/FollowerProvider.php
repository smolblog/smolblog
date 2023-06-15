<?php

namespace Smolblog\Core\Federation;

use Smolblog\Core\Content\Content;

/**
 * Service that handles followers of a blog over a particular protocol.
 *
 * The idea of this interface is to abstract the implementation details of ActivityPub, ATP, or any other federation
 * protocol as it pertains to the model. The model is concerned with sending content to followers; the FollowerProvider
 * handles translating the content into the format used by the protocol.
 */
interface FollowerProvider {
	/**
	 * Unique slug to identify this provider. Usually the protocol name.
	 *
	 * @return string
	 */
	public static function getSlug(): string;

	/**
	 * Send the given content to the given followers.
	 *
	 * @param Content $content   Content being created.
	 * @param array   $followers Array of followers to be notified.
	 * @return void
	 */
	public function sendContentToFollowers(Content $content, array $followers): void;
}
