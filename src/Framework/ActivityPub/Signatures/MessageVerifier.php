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
	 * Sign a PSR-7 Request.
	 *
	 * @param RequestInterface $request Request that needs a signature.
	 * @param string           $keyPem  Public key to use to verify the request.
	 * @return RequestInterface
	 */
	public function verify(RequestInterface $request, string $keyPem): bool {
		// TODO: Not sure how to determine the algorithm used, but everyone seems to use SHA256 right now.
		$digest = 'SHA-256=' . base64_encode(hash('sha256', $body, true));

		$headersToSign = [];
		foreach (explode(' ', $signatureData['headers']) as $h) {
			if ($h == '(request-target)') {
			$headersToSign[$h] = 'post ' . $path;
			} elseif ($h == 'digest') {
			$headersToSign[$h] = $digest;
			} elseif (isset($inputHeaders[$h][0])) {
			$headersToSign[$h] = $inputHeaders[$h][0];
			}
		}
		$signingString = $this->generateSignatureSource($headersToSign);

		$verified = openssl_verify($signingString, base64_decode($signatureData['signature']), $publicKey, OPENSSL_ALGO_SHA256);

		return [$verified, $signingString];

		return false;
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
}
