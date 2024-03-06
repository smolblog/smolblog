<?php

namespace Smolblog\ActivityPub\Handles;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Framework\Objects\Identifier;

/**
 * Store ActivityPub handles for a site.
 */
class HandleProjection implements Projection {
	public const TABLE = 'activitypub_handles';

	/**
	 * Construct the projection.
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(
		private ConnectionInterface $db
	) {
	}

	/**
	 * Create a new ActivityPub handle.
	 *
	 * @param ActivityPubHandleCreated $event Event to handle.
	 * @return void
	 */
	public function onActivityPubHandleCreated(ActivityPubHandleCreated $event) {
		$this->db->table(self::TABLE)->insert([
			'handle_uuid' => $event->handleId->toString(),
			'handle' => $event->handle,
			'site_uuid' => $event->siteId->toString(),
		]);
	}

	/**
	 * Get the ActivityPub handle for a site.
	 *
	 * @param GetHandleForSite $query Query to execute.
	 * @return void
	 */
	public function onGetHandleForSite(GetHandleForSite $query) {
		$result = $this->db->table(self::TABLE)->where('site_uuid', '=', $query->siteId->toString())->value('handle');

		$query->setResults($result);
	}

	/**
	 * Get the site for a given ActivityPub handle.
	 *
	 * @param GetSiteByHandle $query Query to execute.
	 * @return void
	 */
	public function onGetSiteByHandle(GetSiteByHandle $query) {
		$result = $this->db->table(self::TABLE)->where('handle', '=', $query->handle)->value('site_uuid');

		$query->setResults(isset($result) ? Identifier::fromString($result) : null);
	}
}
