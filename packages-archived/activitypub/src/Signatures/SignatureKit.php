<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Psr\Http\Message\RequestInterface;

/**
 * Useful functions for dealing with HTTP signatures.
 *
 * This trait largely exists so that these functions can be tested in isolation. With how finicky HTTP signatures
 * have proven to be, it's helpful to know that individual pieces are behaving correctly.
 *
 * Based on https://github.com/aaronpk/Nautilus/blob/main/app/ActivityPub/HTTPSignature.php
 * from https://github.com/aaronpk/Nautilus licensed under Apache 2.0
 */
trait SignatureKit {
	/**
	 * Add a SHA-256 digest to the given request.
	 *
	 * @param RequestInterface $request Request to modify.
	 * @return RequestInterface
	 */
	private function addDigest(RequestInterface $request): RequestInterface {
		if ($request->hasHeader('digest') || !$request->getBody()->getSize()) {
			return $request;
		}

		$digest = base64_encode(hash('sha256', $request->getBody()->__toString(), true));
		return $request->withAddedHeader('Digest', "sha-256=$digest");
	}

	/**
	 * Add the current date and time to the given request.
	 *
	 * @param RequestInterface $request Request to modify.
	 * @return RequestInterface
	 */
	private function addDate(RequestInterface $request): RequestInterface {
		if ($request->hasHeader('date')) {
			return $request;
		}

		$date = new DateTimeImmutable(timezone: new DateTimeZone('UTC'));
		return $request->withAddedHeader('Date', $date->format(DateTimeInterface::RFC7231));
	}

	/**
	 * Generate the string that will be signed.
	 *
	 * @param RequestInterface $request Request to sign.
	 * @param array            $headers Headers to use.
	 * @return string
	 */
	private function generateSignatureSource(RequestInterface $request, array $headers): string {
		$sigLines = array_map(
			fn($key) => strtolower($key) . ': ' . match (strtolower($key)) {
				'(request-target)' => strtolower($request->getMethod()) . ' ' . $request->getRequestTarget(),
				default => $request->getHeaderLine($key)
			},
			$headers,
		);

		return implode(separator: "\n", array: $sigLines);
	}

	/**
	 * Generate a Signature header from an array of key/value parameters.
	 *
	 * @param array $params Key/value array of parameters.
	 * @return string
	 */
	private function signatureHeaderFromParts(array $params): string {
		return implode(separator: ',', array: array_map(
			fn($key, $val) => "$key=\"$val\"",
			array_keys($params),
			array_values($params),
		));
	}

	/**
	 * Parse the Signature header into keys and values.
	 *
	 * @param string $header HTTP Signature header.
	 * @return array
	 */
	private function getSignatureHeaderParts(string $header): array {
		$num = preg_match_all('/([\w]+)="([^"]+)"/', $header, $matches, PREG_PATTERN_ORDER);

		if (!$num) {
			return [];
		}

		return array_combine($matches[1], $matches[2]);
	}
}
