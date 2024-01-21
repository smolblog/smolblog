<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
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
	 * @param ClientInterface $fetcher Client to send HTTP messages.
	 * @param MessageSigner   $signer  Service to sign the HTTP message.
	 */
	public function __construct(
		private ClientInterface $fetcher,
		private ?MessageSigner $signer = null,
	) {
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
	 * @return ActivityPubObject
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
			headers: ['accept' => 'application/json'],
		);

		if ($signedWithPrivateKey && $this->signer) {
			$request = $this->signer->sign(
				request: $request,
				keyId: $withKeyId,
				keyPem: $signedWithPrivateKey,
			);
		}

		$response = $this->fetcher->sendRequest($request);

		return ActivityPubBase::typedObjectFromArray(
			json_decode($response->getBody()->getContents(), associative: true)
		);
	}
}
