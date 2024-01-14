<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use phpseclib3\Crypt\PublicKeyLoader;
use Psr\Http\Message\RequestInterface;

/**
 * Verify HTTP requests.
 *
 * Based on https://github.com/aaronpk/Nautilus/blob/main/app/ActivityPub/HTTPSignature.php
 * from https://github.com/aaronpk/Nautilus licensed under Apache 2.0
 */
class MessageVerifier {
	use SignatureKit;

	/**
	 * Verify that the given request is signed by the given key.
	 *
	 * If the request is not signed, this function will return false.
	 *
	 * @param RequestInterface $request Request that needs a signature.
	 * @param string           $keyPem  Public key to use to verify the request.
	 * @return RequestInterface
	 */
	public function verify(RequestInterface $request, string $keyPem): bool {
		if (!$request->hasHeader('signature')) {
			// This function answers the question "Is this request signed by this key?"
			// If the request is not signed, then the answer is "no".
			return false;
		}

		if (!$this->verifyDigest($request)) {
			// If the digest does not match, the signature is invalid anyway.
			return false;
		}

		$parts = $this->getSignatureHeaderParts($request->getHeaderLine('signature'));
		$signingString = $this->generateSignatureSource($request, explode(' ', $parts['headers']));

		$key = PublicKeyLoader::loadPublicKey($keyPem);
		return $key->verify($signingString, $parts['signature']);
	}

	/**
	 * Get the keyId used to sign this request.
	 *
	 * @param RequestInterface $request Signed request.
	 * @return string
	 */
	public function getKeyId(RequestInterface $request): string {
		return $this->getSignatureHeaderParts($request->getHeaderLine('signature'))['keyId'] ?? '';
	}

	/**
	 * Verify that the Digest header matches the hash of the request body.
	 *
	 * - Lack of both a digest and body will return TRUE.
	 * - Lack of either a digest or body (but not both) will return FALSE.
	 *
	 * @param RequestInterface $request Request to verify.
	 * @return boolean
	 */
	public function verifyDigest(RequestInterface $request): bool {
		if ($request->getBody()->getSize() === 0 && !$request->hasHeader('digest')) {
			return true;
		}

		$digest = base64_encode(hash('sha256', $request->getBody()->__toString(), true));

		return "SHA256=$digest" === $request->getHeaderLine('digest');
	}
}
