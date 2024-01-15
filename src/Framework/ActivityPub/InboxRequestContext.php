<?php

namespace Smolblog\Framework\ActivityPub;

use Smolblog\Framework\ActivityPub\Objects\Actor;

/**
 * Context for a web request to an ActivityPub inbox.
 */
readonly class InboxRequestContext {
	/**
	 * Construct the object.
	 *
	 * @param mixed       $inboxKey      Identification for the particular inbox hit.
	 * @param Actor|null  $inboxActor    Actor whose inbox is being hit.
	 * @param string|null $privateKeyPem Private key for the inbox actor.
	 */
	public function __construct(
		public mixed $inboxKey,
		public ?Actor $inboxActor = null,
		public ?string $privateKeyPem = null,
	) {
	}
}
