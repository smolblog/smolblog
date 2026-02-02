<?php

namespace Smolblog\Core\Connection\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Delete a Connection.
 */
readonly class DeleteConnection implements Command, Authenticated {
	use ValueKit;

	/**
	 * Construct the query.
	 *
	 * @param UuidInterface $userId       User making the request.
	 * @param UuidInterface $connectionId Connection to delete.
	 */
	public function __construct(
		public readonly UuidInterface $userId,
		public readonly UuidInterface $connectionId,
	) {}
}
