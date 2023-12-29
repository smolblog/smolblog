<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Message\RequestInterface;

/**
 * Service that can sign a web request with a given key.
 */
interface MessageSigner {
	/**
	 * Sign a PSR-7 Request.
	 *
	 * @param RequestInterface $request Request that needs a signature.
	 * @param string           $keyId   Name of the key to use in the signature.
	 * @param string           $keyPem  Private key to use to sign the request.
	 * @return RequestInterface
	 */
	public function sign(RequestInterface $request, string $keyId, string $keyPem): RequestInterface;
}
