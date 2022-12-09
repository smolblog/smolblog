<?php

namespace Smolblog\Core\Importer;

use Smolblog\Framework\Command;
use Smolblog\Framework\Identifier;

/**
 * Pull posts from the indicated channel. Optionally provide pagination info.
 */
readonly class PullFromChannel extends Command {
	/**
	 * Construct the command.
	 *
	 * @param Identifier $channelId ID of channel to pull from.
	 * @param array      $options   Optional info, such as page size or exclusions.
	 */
	public function __construct(
		public Identifier $channelId,
		public array $options = []
	) {
	}
}
