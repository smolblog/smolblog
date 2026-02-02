<?php

namespace Smolblog\Core\Connection\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Check the connection to see if any access tokens need to be refreshed.
 */
readonly class RefreshConnection implements Command, Authenticated {
	use ValueKit;

	/**
	 * Create the command
	 *
	 * @param UuidInterface $connectionId Connection to refresh.
	 * @param UuidInterface $userId       User initiating the refresh.
	 */
	public function __construct(
		public UuidInterface $connectionId,
		public UuidInterface $userId,
	) {}
}
