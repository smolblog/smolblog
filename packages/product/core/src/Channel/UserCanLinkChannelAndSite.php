<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Check if a given user can link a given channel to a given site.
 */
class UserCanLinkChannelAndSite extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId    ID of the user in question.
	 * @param Identifier $channelId ID of the channel in question.
	 * @param Identifier $siteId    ID of the site in question.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $channelId,
		public readonly Identifier $siteId,
	) {
	}
}
