<?php

namespace Smolblog\Core\Media\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;

/**
 * Gives a truthy value if the given user can edit the given media object.
 */
class UserCanEditMedia extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId  User to check.
	 * @param Identifier $mediaId Media to check.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $mediaId,
	) {
	}
}
