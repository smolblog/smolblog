<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\Queries\ExtensableContentQuery;
use Smolblog\Core\Content\Queries\ExtensableContentQueryKit;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\MemoizableQueryKit;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get a Status by its id.
 */
class StatusById extends Query implements MemoizableQuery, ExtensableContentQuery {
	use MemoizableQueryKit;
	use ExtensableContentQueryKit;

	/**
	 * Construct the query
	 *
	 * @param Identifier $id Content ID of the status.
	 */
	public function __construct(public readonly Identifier $id) {
	}
}
