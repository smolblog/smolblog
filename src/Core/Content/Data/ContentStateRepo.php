<?php

namespace Smolblog\Core\Content\Data;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Framework\Messages\Projection;

class ContentStateRepo implements Projection {
	public const TABLE = 'content_repo';

	/**
	 * Construct the service.
	 *
	 * @param ConnectionInterface $db  Working DB connection.
	 */
	public function __construct(
		private ConnectionInterface $db,
	) {
	}
}
