<?php

namespace Smolblog\Tumblr;

use Tumblr\API\Client as TumblrClient;

class TumblrClientFactory {
	private TumblrClient $appClient;
	private array $userClients = [];

	public function __construct(
		private string $appKey,
		private string $appSecret,
	)
	{

	}

	public function getAppClient(): TumblrClient {
		$this->appClient ??= new TumblrClient($this->appKey, $this->appSecret);
		return $this->appClient;
	}

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
