<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Core\Connector\Queries\ConnectionBelongsToUser;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Delete a Connection.
 */
class DeleteConnection extends Command implements AuthorizableMessage {
	use StoppableMessageKit;

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
	public function getAuthorizationQuery(): Query {
		return new ConnectionBelongsToUser(
			connectionId: $this->connectionId,
			userId: $this->userId,
		);
	}
}
