<?php

namespace Smolblog\Tumblr;

use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\SiteHasPermissionForChannel;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Begin an iteration of a Tumblr import.
 *
 * This command contains full objects as it is intended to be sent asynchronously.
 */
class ImportFromTumblr extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * @throws InvalidCommandParametersException When connection or channel are invalid.
	 *
	 * @param Connection   $connection Connection to authenticate with.
	 * @param Channel      $channel    Channel to pull content from.
	 * @param Identifier   $userId     User initiating this import.
	 * @param Identifier   $siteId     Site to import to.
	 * @param integer|null $before     Pull posts before this UNIX timestamp.
	 */
	public function __construct(
		public readonly Connection $connection,
		public readonly Channel $channel,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly ?int $before = null,
	) {
		if ($connection->id != $channel->connectionId || $connection->provider !== 'tumblr') {
			throw new InvalidCommandParametersException(
				command: $this,
				message: 'Connection must be for Tumblr and Channel must belong to Connection.',
			);
		}
	}

	/**
	 * Confirm that this site can pull from this channel.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new SiteHasPermissionForChannel(
			siteId: $this->siteId,
			channelId: $this->channel->id,
			mustPull: true,
		);
	}
}
