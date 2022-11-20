<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Value;

/**
 * Data required from a Connector to initialize an OAuth2 request.
 */
class ConnectorInitData extends Value {
	/**
	 * URL to show the user/redirect the user to.
	 *
	 * @var string
	 */
	public readonly string $url;

	/**
	 * OAuth2 State, a random string that will be given back to identify the
	 * request in the callback.
	 *
	 * @var string
	 */
	public readonly string $state;

	/**
	 * Any additional information needed by the callback function.
	 *
	 * @var array
	 */
	public readonly array $info;

	/**
	 * Create the data object.
	 *
	 * @param string $url   URL to show the user.
	 * @param string $state Random string to identify the request.
	 * @param array  $info  Additional info needed by the callback.
	 */
	public function __construct(string $url, string $state, array $info) {
		$this->url = $url;
		$this->state = $state;
		$this->info = $info;
	}
}
