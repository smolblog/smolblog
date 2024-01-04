<?php

namespace Smolblog\Framework\Infrastructure;

use DateTimeInterface;
use HttpSignatures\Context;
use Psr\Http\Message\RequestInterface;
use Smolblog\Framework\ActivityPub\MessageSigner;
use Smolblog\Framework\ActivityPub\MessageVerifier;

/**
 * Service to sign a PSR-7 request with a given key.
 */
class HttpSigner implements MessageSigner, MessageVerifier {
	/**
	 * Sign a PSR-7 Request.
	 *
	 * @param RequestInterface $request Request that needs a signature.
	 * @param string           $keyId   Name of the key to use in the signature.
	 * @param string           $keyPem  Private key to use to sign the request.
	 * @return RequestInterface
	 */
	public function sign(RequestInterface $request, string $keyId, string $keyPem): RequestInterface {
		$context = new Context([
			'keys' => [$keyId => $keyPem],
			'algorithm' => 'rsa-sha256',
			'headers' => ['(request-target)', 'Date', 'Host'],
		]);

		if (!$request->hasHeader('Date')) {
			$request = $request->withAddedHeader('Date', date(DateTimeInterface::RFC7231));
		}

		return $context->signer()->signWithDigest($request);
	}

	/**
	 * Verify a signed a PSR-7 Request.
	 *
	 * @param RequestInterface $request Request with a signature.
	 * @param string           $keyId   Name of the key used in the signature.
	 * @param string           $keyPem  Public key to use to verify the request.
	 * @return boolean
	 */
	public function verify(RequestInterface $request, string $keyId, string $keyPem): bool {
		$context = new Context([
			'keys' => [$keyId => $keyPem],
		]);

		return $context->verifier()->isSignedWithDigest($request);
	}
}
