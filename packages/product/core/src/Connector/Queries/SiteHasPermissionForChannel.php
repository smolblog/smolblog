<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Check if a given site can push to/pull from a given channel.
 */
class SiteHasPermissionForChannel extends Query {
	/**
	 * Construct the query
	 *
	 * @param Identifier $siteId    ID of site in question.
	 * @param Identifier $channelId ID of channel in question.
	 * @param boolean    $mustPush  True if permissions must include Push.
	 * @param boolean    $mustPull  True if permissions must include Pull.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $channelId,
		public readonly bool $mustPush = false,
		public readonly bool $mustPull = false,
	) {
	}
}
