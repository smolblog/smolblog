<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get existing and available connections and channels for the site admin screen.
 *
 * The site admin screen has a use case that isn't easily met by the existing queries. It needs the channels that are
 * currently linked to the site as well as any channels the current user has that are not linked to the site. And it
 * needs basic connection info for all of those as well to facilitate the UI. With piecemeal queries, this could get
 * unwieldy very quickly and involve a LOT more trips to the database than is necessary. And the point of dedicated
 * query objects is to get data retrieval as close to the data as possible.
 *
 * Query results should be an associative array:
 * [
 * 	 'connections' => [ Connections ],
 *   'channels'    => [ Connection.id => [ Channels ] ],
 *   'links'       => [ Channel.id => SiteChannelLink ]
 * ]
 */
class ChannelsForAdmin extends Query implements AuthorizableMessage {
	/**
	 * Create the query.
	 *
	 * @param Identifier $siteId ID for the site.
	 * @param Identifier $userId ID of the user.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
	) {
	}

	/**
	 * User must have admin permissions for the site.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(
			siteId: $this->siteId,
			userId: $this->userId,
			mustBeAdmin: true,
		);
	}
}
