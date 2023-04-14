<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Core\Connector\Queries\UserCanLinkChannelAndSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Give a Site permission to push from/pull to a Channel.
 */
class LinkChannelToSite extends Command implements AuthorizableMessage {
	/**
	 * Construct the command
	 *
	 * @param Identifier $channelId ID of the Channel to link.
	 * @param Identifier $siteId    ID of the Site to link.
	 * @param Identifier $userId    ID of the User initiating this command.
	 * @param boolean    $canPush   Set if this Site can push to the Channel.
	 * @param boolean    $canPull   Set if this Site can pull from the Channel.
	 */
	public function __construct(
		public readonly Identifier $channelId,
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly bool $canPush,
		public readonly bool $canPull,
	) {
	}

	/**
	 * Get the Query to check if this Command can execute.
	 *
	 * @return UserCanLinkChannelAndSite
	 */
	public function getAuthorizationQuery(): UserCanLinkChannelAndSite {
		return new UserCanLinkChannelAndSite(
			userId: $this->userId,
			channelId: $this->channelId,
			siteId: $this->siteId,
		);
	}
}
