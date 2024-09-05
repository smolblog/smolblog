<?php

namespace Smolblog\Core\Connection\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Data required from a Connector to initialize an OAuth2 request.
 */
readonly class ConnectionInitData extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Create the data object.
	 *
	 * @param string $url   URL to show the user.
	 * @param string $state Random string to identify the request.
	 * @param array  $info  Additional info needed by the callback.
	 */
	public function __construct(public string $url, public string $state, public array $info) {
	}
}
