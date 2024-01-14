<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use phpseclib3\Crypt\PublicKeyLoader;
use Psr\Http\Message\RequestInterface;

/**
 * Sign and verify HTTP requests.
 *
 * Based on https://github.com/aaronpk/Nautilus/blob/main/app/ActivityPub/HTTPSignature.php
 * from https://github.com/aaronpk/Nautilus licensed under Apache 2.0
 */
class MessageSigner {
	use SignatureKit;

	/**
	 * Sign a PSR-7 Request.
	 *
	 * @param RequestInterface $request Request that needs a signature.
	 * @param string           $keyId   Name of the key to use in the signature.
	 * @param string           $keyPem  Private key to use to sign the request.
	 * @return RequestInterface
	 */
	public function sign(RequestInterface $request, string $keyId, string $keyPem): RequestInterface {
		$headerParams = [
			'keyId' => $keyId,
			'algorithm' => 'rsa-sha256',
		];

		$headersToSign = [
			'(request-target)',
			'host',
			'date',
		];

		$finalRequest = $this->addDate($request);
		$finalRequest = $this->addDigest($finalRequest);

		if ($finalRequest->hasHeader('digest')) {
			$headersToSign[] = 'digest';
		}
		if ($finalRequest->hasHeader('content-type')) {
			$headersToSign[] = 'content-type';
		}

		$headerParams['headers'] = implode(separator: ' ', array: $headersToSign);

		$stringToSign = $this->generateSignatureSource(request: $finalRequest, headers: $headersToSign);
		$key = PublicKeyLoader::loadPrivateKey($keyPem);
		$headerParams['signature'] = base64_encode($key->sign($stringToSign));

		return $finalRequest->withAddedHeader('Signature', $this->signatureHeaderFromParts($headerParams));
	}
}
