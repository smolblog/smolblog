<?php

namespace Smolblog\Framework\Infrastructure;

use JsonSerializable;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Default HttpClient; uses GuzzleHttp\Client internally.
 */
class DefaultHttpClient implements HttpClient {
	/**
	 * Create the service
	 *
	 * @param Client $internal GuzzleHttp Client instance.
	 */
	public function __construct(private Client $internal) {
	}

	/**
	 * Initiate an HTTP request and return the result.
	 *
	 * @param string                             $url        URL to fetch.
	 * @param string                             $method     HTTP method to use; default GET.
	 * @param array|null                         $headers    Headers to use; default empty.
	 * @param array|JsonSerializable|string|null $body       Body of the request; default empty.
	 * @param string|null                        $signedWith Optional key to use to sign the request.
	 * @return ResponseInterface
	 */
	public function request(
		string $url,
		string $method = 'GET',
		?array $headers = null,
		array|JsonSerializable|string|null $body = null,
		?string $signedWith = null,
	): ResponseInterface {
		$options = [];
		if (isset($headers)) {}
		if (isset($body)) {
			if (is_array($body) || is_object($body)) {
				$options['json'] = $body;
			} else {
				$options['body'] = $body;
			}
		}

		return $this->internal->request($method, $url, $options);
	}
}
