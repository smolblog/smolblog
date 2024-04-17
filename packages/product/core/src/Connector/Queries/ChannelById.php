<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Find a Channel via its ID; returns a single Channel object.
 *
 * If the Connection ID and channel Key are known, the Channel ID can be created using the static
 * Smolblog\Core\Connector\Entities\Channel::buildId function.
 */
readonly class ChannelById extends Query {
	/**
	 * Construct the query
	 *
	 * @param Identifier $channelId ID of the channel to fetch.
	 */
	public function __construct(
		public readonly Identifier $channelId,
	) {
		parent::__construct();
	}
}
