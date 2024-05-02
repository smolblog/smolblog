<?php

namespace Smolblog\Foundation\Value\Http;

use GuzzleHttp\Psr7\Request;
use JsonSerializable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Undocumented class
 */
class HttpRequest implements RequestInterface {
	use HttpMessageKit;

	/**
	 * Store the Guzzle object backing this object.
	 *
	 * @var Request
	 */
	private Request $internal;

	/**
	 * Create the Request.
	 *
	 * @param HttpVerb                           $verb    HTTP method to use.
	 * @param string|UriInterface                $url     URL of the request.
	 * @param array<string, string>              $headers Headers of the request.
	 * @param string|array|JsonSerializable|null $body    Request body in string or object format.
	 */
	public function __construct(
		HttpVerb $verb,
		string|UriInterface $url,
		array $headers = [],
		string|array|JsonSerializable|null $body = null,
	) {
		$casedHeaders = array_change_key_case($headers, CASE_LOWER);

		$parsedBody = $body;
		if (isset($body) && (is_array($body) || is_object($body))) {
			$parsedBody = json_encode($body);
			$casedHeaders['content-type'] ??= 'application/json';
		}

		$this->internal = new Request($verb->value, $url, $casedHeaders, $parsedBody);
	}

  /**
   * Retrieves the message's request target.
   *
   * Retrieves the message's request-target either as it will appear (for
   * clients), as it appeared at request (for servers), or as it was
   * specified for the instance (see withRequestTarget()).
   *
   * In most cases, this will be the origin-form of the composed URI,
   * unless a value was provided to the concrete implementation (see
   * withRequestTarget() below).
   *
   * If no URI is available, and no request-target has been specifically
   * provided, this method MUST return the string "/".
   *
   * @return string
   */
	public function getRequestTarget(): string {
			return $this->internal->getRequestTarget();
	}

	/**
	 * Return an instance with the specific request-target.
	 *
	 * If the request needs a non-origin-form request-target — e.g., for
	 * specifying an absolute-form, authority-form, or asterisk-form —
	 * this method may be used to create an instance with the specified
	 * request-target, verbatim.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * changed request target.
	 *
	 * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
	 *     request-target forms allowed in request messages)
	 * @param string $requestTarget New target of the message.
	 * @return static
	 */
	public function withRequestTarget(string $requestTarget): RequestInterface {
			return $this->internal->withRequestTarget($requestTarget);
	}


	/**
	 * Retrieves the HTTP method of the request.
	 *
	 * @return string Returns the request method.
	 */
	public function getMethod(): string {
			return $this->internal->getMethod();
	}

	/**
	 * Return an instance with the provided HTTP method.
	 *
	 * While HTTP method names are typically all uppercase characters, HTTP
	 * method names are case-sensitive and thus implementations SHOULD NOT
	 * modify the given string.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * changed request method.
	 *
	 * @param string $method Case-sensitive method.
	 * @return static
	 * @throws \InvalidArgumentException For invalid HTTP methods.
	 */
	public function withMethod(string $method): RequestInterface {
			return $this->internal->withMethod($method);
	}

	/**
	 * Retrieves the URI instance.
	 *
	 * This method MUST return a UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @return UriInterface Returns a UriInterface instance
	 *     representing the URI of the request.
	 */
	public function getUri(): UriInterface {
			return $this->internal->getUri();
	}

	/**
	 * Returns an instance with the provided URI.
	 *
	 * This method MUST update the Host header of the returned request by
	 * default if the URI contains a host component. If the URI does not
	 * contain a host component, any pre-existing Host header MUST be carried
	 * over to the returned request.
	 *
	 * You can opt-in to preserving the original state of the Host header by
	 * setting `$preserveHost` to `true`. When `$preserveHost` is set to
	 * `true`, this method interacts with the Host header in the following ways:
	 *
	 * - If the Host header is missing or empty, and the new URI contains
	 *   a host component, this method MUST update the Host header in the returned
	 *   request.
	 * - If the Host header is missing or empty, and the new URI does not contain a
	 *   host component, this method MUST NOT update the Host header in the returned
	 *   request.
	 * - If a Host header is present and non-empty, this method MUST NOT update
	 *   the Host header in the returned request.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @param UriInterface $uri          New request URI to use.
	 * @param boolean      $preserveHost Preserve the original state of the Host header.
	 * @return static
	 */
	public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface {
			return $this->internal->withUri($uri, $preserveHost);
	}
}
