<?php

namespace Smolblog\Tumblr;

use Tumblr\API\Client as TumblrClient;

/**
 * Get fully initialized Tumblr clients.
 */
class TumblrClientFactory {
	/**
	 * Generic client not tied to a user.
	 *
	 * @var TumblrClient
	 */
	private TumblrClient $appClient;

	/**
	 * Any created clients tied to users.
	 *
	 * @var array
	 */
	private array $userClients = [];

	/**
	 * Construct the service.
	 *
	 * @param string $appKey    Application key.
	 * @param string $appSecret Application secret.
	 */
	public function __construct(
		private string $appKey,
		private string $appSecret,
	) {
	}

	/**
	 * Get a generic client tied to the application but no specific user.
	 *
	 * @return TumblrClient
	 */
	public function getAppClient(): TumblrClient {
		$this->appClient ??= new TumblrClient($this->appKey, $this->appSecret);
		return $this->appClient;
	}

	/**
	 * Get a client tied to the application and the user represented by the given key and secret.
	 *
	 * @param string $key    User key.
	 * @param string $secret User secret key.
	 * @return TumblrClient
	 */
	public function getUserClient(string $key, string $secret): TumblrClient {
		$this->userClients[$key . $secret] ??= new TumblrClient(
			$this->appKey,
			$this->appSecret,
			$key,
			$secret,
		);

		return $this->userClients[$key . $secret];
	}
}
