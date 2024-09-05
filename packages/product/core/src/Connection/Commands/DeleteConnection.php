<?php

namespace Smolblog\Core\Connection\Commands;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Delete a Connection.
 */
readonly class DeleteConnection extends Command {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId       User making the request.
	 * @param Identifier $connectionId Connection to delete.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $connectionId,
	) {
		parent::__construct();
	}
}
