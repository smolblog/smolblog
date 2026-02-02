<?php

namespace Smolblog\Core\Channel\Data;

use Smolblog\Core\Channel\Entities\Channel;
use Ramsey\Uuid\UuidInterface;

interface ChannelRepo {
	/**
	 * Get a Channel.
	 *
	 * @param UuidInterface $channelId ID of the Channel.
	 * @return Channel|null
	 */
	public function channelById(UuidInterface $channelId): ?Channel;

	/**
	 * Get all Channels linked to a particular Connection.
	 *
	 * @param UuidInterface $connectionId ID of Connection in question.
	 * @return Channel[]
	 */
	public function channelsForConnection(UuidInterface $connectionId): array;

	/**
	 * Get all Channels linked to a Site.
	 *
	 * @param UuidInterface $siteId ID of Site to check.
	 * @return Channel[]
	 */
	public function channelsForSite(UuidInterface $siteId): array;

	/**
	 * Check if a given site can push to a given channel.
	 *
	 * @param UuidInterface $siteId    ID of site in question.
	 * @param UuidInterface $channelId ID of channel in question.
	 * @return boolean
	 */
	public function siteCanUseChannel(UuidInterface $siteId, UuidInterface $channelId): bool;
}
