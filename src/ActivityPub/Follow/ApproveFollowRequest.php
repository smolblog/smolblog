<?php

namespace Smolblog\ActivityPub\Follow;

use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Approve the given Follow request.
 *
 * All requests are currently auto-approved; this command exists so the response can be sent async.
 */
class ApproveFollowRequest extends Command {
	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId  ID of the site being followed.
	 * @param Identifier $userId  User approving the request.
	 * @param Follow     $request Request being approved.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Follow $request,
	) {
	}
}
