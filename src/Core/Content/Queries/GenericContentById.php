<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\MemoizableQueryKit;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get a given Content object as a GenericContent object.
 */
class GenericContentById extends Query implements MemoizableQuery, ExtensableContentQuery {
	use MemoizableQueryKit;

	/**
	 * Construct the query.
	 *
	 * @param Identifier $id Content ID.
	 */
	public function __construct(public readonly Identifier $id) {
	}
}
