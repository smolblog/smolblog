<?php

namespace Smolblog\Core\Federation;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Framework\Objects\Identifier;

/**
 * Track followers for a site.
 */
class FollowerProjection implements Projection {
	public const TABLE = 'followers';

	/**
	 * Construct the projection.
	 *
	 * @param ConnectionInterface $db Working DB connection.
	 */
	public function __construct(
		private ConnectionInterface $db
	) {
	}

	/**
	 * Record a new follower.
	 *
	 * @param FollowerAdded $event Event to handle.
	 * @return void
	 */
	public function onFollowerAdded(FollowerAdded $event) {
		$follower = $event->getFollower();

		$this->db->table(self::TABLE)->upsert(
			[
				'follower_uuid' => $follower->id->toString(),
				'site_uuid' => $follower->siteId->toString(),
				'provider' => $follower->provider,
				'provider_key' => $follower->providerKey,
				'display_name' => $follower->displayName,
				'details' => json_encode($follower->details),
			],
			'follower_uuid',
			['display_name', 'details']
		);
	}

	/**
	 * Get followers for a given site.
	 *
	 * @param GetFollowersForSiteByProvider $query Query to fetch.
	 * @return void
	 */
	public function onFollowersForSite(GetFollowersForSiteByProvider $query) {
		$results = $this->db->table(self::TABLE)->where('site_uuid', '=', $query->siteId->toString())->get();

		$query->setResults(
			array_map(
				fn($grp) => $grp->all(),
				$results->map(fn($row) => self::followerFromRow($row))
					->groupBy(fn($fl) => $fl->provider)
					->all()
			)
		);
	}

	/**
	 * Translate a database row into a Follower object.
	 *
	 * @param object $data Database row.
	 * @return Follower
	 */
	public static function followerFromRow(object $data): Follower {
		return new Follower(
			siteId: Identifier::fromString($data->site_uuid),
			provider: $data->provider,
			providerKey: $data->provider_key,
			displayName: $data->display_name,
			details: json_decode($data->details, true),
		);
	}
}
