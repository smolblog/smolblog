<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Core\Connector\Queries\ConnectionBelongsToUser;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Command to fetch and save an updated list of channels for a given Connection.
 */
class RefreshChannels extends Command implements AuthorizableMessage {
	/**
	 * Construct the command
	 *
	 * @param Identifier $connectionId ID of the Connection to get channels for.
	 * @param Identifier $userId       ID of the user initiating this Command.
	 */
	public function __construct(
		public readonly Identifier $connectionId,
		public readonly Identifier $userId,
	) {
	}

	/**
	 * Check if the given User has permission to execute this Command.
	 *
	 * @return ConnectionBelongsToUser
	 */
	public function getAuthorizationQuery(): ConnectionBelongsToUser {
		return new ConnectionBelongsToUser(connectionId: $this->connectionId, userId: $this->userId);
	}
}
