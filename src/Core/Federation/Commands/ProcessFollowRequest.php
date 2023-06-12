<?php

namespace Smolblog\Core\Federation\Commands;

use Smolblog\Core\Federation\Objects\Actor;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Tell the Model to process an incoming Follow request.
 */
class ProcessFollowRequest extends Command {
	/**
	 * Create the command
	 *
	 * @param string     $requestId ID/URI of the ActivityPub Action.
	 * @param Identifier $siteId    Site being followed.
	 * @param Actor      $actor     Actor making the request.
	 */
	public function __construct(
		public readonly string $requestId,
		public readonly Identifier $siteId,
		public readonly Actor $actor,
	) {
	}
}
