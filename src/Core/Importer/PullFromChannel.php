<?php

namespace Smolblog\Core\Importer;

use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Pull posts from the indicated channel. Optionally provide pagination info.
 */
class PullFromChannel extends Command {
	/**
	 * Construct the command.
	 *
	 * @param Identifier $channelId ID of channel to pull from.
	 * @param array      $options   Optional info, such as page size or exclusions.
	 */
	public function __construct(
		public readonly Identifier $channelId,
		public readonly array $options = []
	) {
	}
}
