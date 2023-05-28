<?php

namespace Smolblog\Framework\Infrastructure;

use DateTimeInterface;
use HttpSignatures\Context;
use Psr\Http\Message\RequestInterface;

/**
 * Service to sign a PSR-7 request with a given key.
 */
class HttpSigner {
	/**
	 * Sign a PSR-7 Request.
	 *
	 * @param RequestInterface $request Request that needs a signature.
	 * @param string           $key     Private key to use to sign the request.
	 * @return RequestInterface
	 */
	public function sign(RequestInterface $request, string $key): RequestInterface {
		$context = new Context([
			'keys' => ['key' => $key],
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
	 * @param string           $key     Public key to use to verify the request.
	 * @return boolean
	 */
	public function verify(RequestInterface $request, string $key): bool {
		$context = new Context([
			'keys' => ['key' => $key],
			'algorithm' => 'rsa-sha256',
			'headers' => ['(request-target)', 'Date', 'Host'],
		]);

		if (!$request->hasHeader('Date')) {
			$request = $request->withAddedHeader('Date', date(DateTimeInterface::RFC7231));
		}

		return $context->verifier()->isSignedWithDigest($request);
	}
}
