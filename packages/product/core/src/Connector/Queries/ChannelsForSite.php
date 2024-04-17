<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Query to get all Connections linked to a Site.
 */
readonly class ChannelsForSite extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier   $siteId  ID of the site.
	 * @param boolean|null $canPush Filter on a value for canPush (blank/null to ignore).
	 * @param boolean|null $canPull Filter on a value for canPull (blank/null to ignore).
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly ?bool $canPush = null,
		public readonly ?bool $canPull = null,
	) {
		parent::__construct();
	}
}
