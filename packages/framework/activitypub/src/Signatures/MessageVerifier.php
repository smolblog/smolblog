<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use DateTimeImmutable;
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
	 * Seconds until an incoming request is considered "expired." Currently 1 day.
	 */
	public const SECONDS_UNTIL_REQUEST_EXPIRED = 60 * 60 * 24;

	/**
	 * Verify the request date, digest, and signature.
	 *
	 * @param RequestInterface $request Request that has a signature.
	 * @param string           $keyPem  Public key to use to verify the request.
	 * @return boolean
	 */
	public function verify(RequestInterface $request, string $keyPem): bool {
		if ($request->hasHeader('date')) {
			$headerDate = new DateTimeImmutable($request->getHeaderLine('date'));
			if (time() - $headerDate->getTimestamp() > self::SECONDS_UNTIL_REQUEST_EXPIRED) {
				return false;
			}
		}

		return $this->verifyDigest($request) && $this->verifySignature($request, $keyPem);
	}

	/**
	 * Verify that the given request is signed by the given key.
	 *
	 * If the request is not signed, this function will return false.
	 *
	 * @param RequestInterface $request Request that has a signature.
	 * @param string           $keyPem  Public key to use to verify the request.
	 * @return RequestInterface
	 */
	public function verifySignature(RequestInterface $request, string $keyPem): bool {
		if (!$request->hasHeader('signature')) {
			// This function answers the question "Is this request signed by this key?"
			// If the request is not signed, then the answer is "no".
			return false;
		}

		$parts = $this->getSignatureHeaderParts($request->getHeaderLine('signature'));
		$signingString = $this->generateSignatureSource($request, explode(' ', $parts['headers']));

		return 1 === openssl_verify($signingString, base64_decode($parts['signature']), $keyPem, OPENSSL_ALGO_SHA256);
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
		$headerLine = $request->getHeaderLine('digest');

		$equalPosition = strpos($headerLine, '=');
		$expected = substr($headerLine, $equalPosition === false ? 0 : $equalPosition + 1);
		$actual = base64_encode(hash('sha256', $request->getBody()->__toString(), true));

		return $expected === $actual;
	}
}
