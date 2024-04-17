<?php

namespace Smolblog\Core\Connector\Events;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates that a Channel has been linked to a Site.
 */
readonly class ChannelSiteLinkSet extends ConnectorEvent {
	/**
	 * Create the event.
	 *
	 * @param Identifier             $channelId    ID of the Channel being linked.
	 * @param Identifier             $siteId       ID of the Site being linked.
	 * @param boolean                $canPush      True if the Site can push content to the Channel.
	 * @param boolean                $canPull      True if the Site can pull content from the Channel.
	 * @param Identifier             $connectionId ID of the connection this event belongs to.
	 * @param Identifier             $userId       ID of the user initiating this change.
	 * @param Identifier|null        $id           Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp    Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly Identifier $channelId,
		public readonly Identifier $siteId,
		public readonly bool $canPush,
		public readonly bool $canPull,
		Identifier $connectionId,
		Identifier $userId,
		Identifier $id = null,
		DateTimeInterface $timestamp = null,
	) {
		parent::__construct(connectionId: $connectionId, userId: $userId, id: $id, timestamp: $timestamp);
	}
}
