<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Framework\Objects\Value;

/**
 * Schema for the create reblog endpoint.
 */
class CreateReblogPayload extends Value {
	/**
	 * Construct the payload
	 *
	 * @throws BadRequest An invalid body was given.
	 *
	 * @param BaseReblogPayload $reblog  Reblog information.
	 * @param boolean           $publish True if it should be published immediately with default settings.
	 */
	public function __construct(
		public readonly BaseReblogPayload $reblog,
		public readonly bool $publish = false,
	) {
		if (!isset($reblog->url)) {
			throw new BadRequest('reblog.url is required.');
		}
	}
}
