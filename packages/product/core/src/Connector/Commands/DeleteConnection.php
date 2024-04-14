<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Core\Connector\Queries\ConnectionBelongsToUser;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Delete a Connection.
 */
readonly class DeleteConnection extends Command implements AuthorizableMessage {
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
	}

	/**
	 * Ensure the owner of the connection is making the request.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): ConnectionBelongsToUser {
		return new ConnectionBelongsToUser(
			connectionId: $this->connectionId,
			userId: $this->userId,
		);
	}
}
