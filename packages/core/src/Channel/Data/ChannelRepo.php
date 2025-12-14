<?php

namespace Smolblog\Core\Channel\Data;

use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Foundation\Value\Fields\Identifier;

interface ChannelRepo {
	/**
	 * Get a Channel.
	 *
	 * @param Identifier $channelId ID of the Channel.
	 * @return Channel|null
	 */
	public function channelById(Identifier $channelId): ?Channel;

	/**
	 * Get all Channels linked to a particular Connection.
	 *
	 * @param Identifier $connectionId ID of Connection in question.
	 * @return Channel[]
	 */
	public function channelsForConnection(Identifier $connectionId): array;

	/**
	 * Get all Channels linked to a Site.
	 *
	 * @param Identifier $siteId ID of Site to check.
	 * @return Channel[]
	 */
	public function channelsForSite(Identifier $siteId): array;

	/**
	 * Check if a given site can push to a given channel.
	 *
	 * @param Identifier $siteId    ID of site in question.
	 * @param Identifier $channelId ID of channel in question.
	 * @return boolean
	 */
	public function siteCanUseChannel(Identifier $siteId, Identifier $channelId): bool;
}
