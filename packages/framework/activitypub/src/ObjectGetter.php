<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubBase;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubObject;
use Smolblog\Framework\ActivityPub\Signatures\MessageSigner;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;

/**
 * Get a remote ActivityPub object, optionally with a signature.
 */
class ObjectGetter {
	/**
	 * Construct the service.
	 *
	 * @param ClientInterface $fetcher      Client to send HTTP messages.
	 * @param MessageSigner   $signer       Optional service to sign the HTTP message.
	 * @param LoggerInterface $log          PSR logger to use.
	 * @param boolean         $throwOnError True to throw exceptions when the remote server gives an error or IDs do
	 *                                      not match.
	 */
	public function __construct(
		private ClientInterface $fetcher,
		private ?MessageSigner $signer = null,
		private ?LoggerInterface $log = null,
		private bool $throwOnError = false,
	) {
		$this->log ??= new NullLogger();
	}

	/**
	 * Get the ActivityPub object at the given URL with an optional key to sign the request with.
	 *
	 * Optionally provide a private key and key ID to sign the request with. Both a key and ID must be provided to sign.
	 *
	 * @throws ActivityPubException When invalid parameters are given or the external service gives an error.
	 *
	 * @param string      $url                  URL to retrieve.
	 * @param string|null $signedWithPrivateKey PEM-formatted private key.
	 * @param string|null $withKeyId            ID of the key.
	 * @return ActivityPubObject|null
	 */
	public function get(
		string $url,
		?string $signedWithPrivateKey = null,
		?string $withKeyId = null
	): ?ActivityPubBase {
		if (($signedWithPrivateKey || $withKeyId) && !($signedWithPrivateKey && $withKeyId)) {
			throw new ActivityPubException('A private key and key ID must both be provided if either is given.');
		}

		$request = new HttpRequest(
			verb: HttpVerb::GET,
			url: $url,
			headers: ['accept' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'],
		);

		if ($signedWithPrivateKey && $this->signer) {
			$request = $this->signer->sign(
				request: $request,
				keyId: $withKeyId,
				keyPem: $signedWithPrivateKey,
			);
		}

		$response = $this->fetcher->sendRequest($request);

		$result = ActivityPubBase::typedObjectFromArray(
			json_decode($response->getBody()->getContents(), associative: true)
		);

		$urlWithoutFragment = str_contains($url, '#') ? substr($url, 0, strpos($url, '#')) : $url;
		if (isset($result?->id) && $result->id !== $urlWithoutFragment) {
			if ($this->throwOnError) {
				throw new ActivityPubException("ID mismatch: object at $urlWithoutFragment has ID $result->id");
			}

			$this->log->error("ID mismatch: object at $urlWithoutFragment has ID $result->id", $result->toArray());
			return null;
		}

		return $result;
	}
}
