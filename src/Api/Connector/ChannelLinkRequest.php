<?php

namespace Smolblog\Api\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Request body for ChannelLink
 */
class ChannelLinkRequest extends Value {
	/**
	 * Construct the request.
	 *
	 * @param Identifier $channelId ID of channel to link.
	 * @param Identifier $siteId    ID of site to link.
	 * @param boolean    $push      True if site should be able to push content.
	 * @param boolean    $pull      True if site should be able to pull content.
	 */
	public function __construct(
		public readonly Identifier $channelId,
		public readonly Identifier $siteId,
		public readonly ?bool $push = null,
		public readonly ?bool $pull = null,
	) {
	}
}
