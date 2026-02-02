<?php

namespace Smolblog\Core\Connection\Entities;

use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * Data required from a Connector to initialize an OAuth2 request.
 */
readonly class ConnectionInitData implements Value {
	use ValueKit;

	/**
	 * Create the data object.
	 *
	 * @param string $url   URL to show the user.
	 * @param string $state Random string to identify the request.
	 * @param array  $info  Additional info needed by the callback.
	 */
	public function __construct(
		public string $url,
		public string $state,
		#[MapType('mixed')] public array $info,
	) {}
}
