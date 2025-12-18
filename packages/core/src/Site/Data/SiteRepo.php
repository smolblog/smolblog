<?php

namespace Smolblog\Core\Site\Data;

use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Site\Entities\Site;

interface SiteRepo {
	/**
	 * Return true if a site with the given ID exists.
	 *
	 * @param UuidInterface $siteId ID to check.
	 * @return boolean
	 */
	public function hasSiteWithId(UuidInterface $siteId): bool;

	/**
	 * Return true if a site with the given key exists.
	 *
	 * @param string $key Key to check.
	 * @return boolean
	 */
	public function hasSiteWithKey(string $key): bool;

	/**
	 * Get the site object for the given ID.
	 *
	 * @param UuidInterface $siteId Site to retrieve.
	 * @return Site|null
	 */
	public function siteById(UuidInterface $siteId): ?Site;

	/**
	 * Get the keypair for the given site.
	 *
	 * On hold for now.
	 *
	 * @param UuidInterface $siteId Site whose keypair to retrieve.
	 * @return Keypair
	 */
	// public function keypairForSite(UuidInterface $siteId): Keypair;

	/**
	 * Get the IDs for users that have permissions for the given site.
	 *
	 * @param UuidInterface $siteId Site whose users to retrieve.
	 * @return UuidInterface[]
	 */
	public function userIdsForSite(UuidInterface $siteId): array;

	/**
	 * Get the sites belonging to a given user.
	 *
	 * @param UuidInterface $userId User whose sites to retrieve.
	 * @return Site[]
	 */
	public function sitesForUser(UuidInterface $userId): array;
}
