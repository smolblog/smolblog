<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Message\RequestInterface;

/**
 * Service that can verify a signed web request.
 */
interface MessageVerifier {
	/**
	 * Verify a signed a PSR-7 Request.
	 *
	 * @param RequestInterface $request Request with a signature.
	 * @param string           $keyId   Name of the key used in the signature.
	 * @param string           $keyPem  Public key to use to verify the request.
	 * @return boolean
	 */
	public function verify(RequestInterface $request, string $keyId, string $keyPem): bool;
}
