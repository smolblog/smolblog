<?php

namespace Smolblog\Core\Importer;

use Smolblog\Core\Command\Command;

/**
 * Pull posts from the indicated channel. Optionally provide pagination info.
 */
class PullFromChannel implements Command {
	/**
	 * Construct the command.
	 *
	 * @param string $channelId ID of channel to pull from.
	 * @param array  $options   Optional info, such as page size or exclusions.
	 */
	public function __construct(
		public readonly string $channelId,
		public readonly array $options = []
	) {
	}
}
