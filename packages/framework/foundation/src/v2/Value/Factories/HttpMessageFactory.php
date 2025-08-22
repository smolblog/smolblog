<?php

namespace Smolblog\Foundation\v2\Value\Factories;

use JsonSerializable;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Smolblog\Foundation\Value\Http\HttpVerb;

/**
 * Static factory for creating PSR-7 HTTP messages.
 *
 * Default implementation is Nyholm\Psr7 but any of the individual factory interfaces can be overwritten.
 */
class HttpMessageFactory {
	/**
	 * Internal RequestFactoryInterface instance.
	 *
	 * @var RequestFactoryInterface
	 */
	private static RequestFactoryInterface $internalRequest;

	/**
	 * Replace the instance of RequestFactoryInterface.
	 *
	 * @param RequestFactoryInterface $newSource RequestFactoryInterface to use.
	 * @return void
	 */
	public static function setRequestSource(RequestFactoryInterface $newSource) {
		self::$internalRequest = $newSource;
	}

	/**
	 * Internal ResponseFactoryInterface instance.
	 *
	 * @var ResponseFactoryInterface
	 */
	private static ResponseFactoryInterface $internalResponse;

	/**
	 * Replace the instance of ResponseFactoryInterface.
	 *
	 * @param ResponseFactoryInterface $newSource ResponseFactoryInterface to use.
	 * @return void
	 */
	public static function setResponseSource(ResponseFactoryInterface $newSource) {
		self::$internalResponse = $newSource;
	}

	/**
	 * Internal StreamFactoryInterface instance.
	 *
	 * @var StreamFactoryInterface
	 */
	private static StreamFactoryInterface $internalStream;

	/**
	 * Replace the instance of StreamFactoryInterface.
	 *
	 * @param StreamFactoryInterface $newSource StreamFactoryInterface to use.
	 * @return void
	 */
	public static function setStreamSource(StreamFactoryInterface $newSource) {
		self::$internalStream = $newSource;
	}

	/**
	 * Internal UriFactoryInterface instance.
	 *
	 * @var UriFactoryInterface
	 */
	private static UriFactoryInterface $internalUri;

	/**
	 * Replace the instance of UriFactoryInterface.
	 *
	 * @param UriFactoryInterface $newSource UriFactoryInterface to use.
	 * @return void
	 */
	public static function setUriSource(UriFactoryInterface $newSource) {
		self::$internalUri = $newSource;
	}

	/**
	 * Internal Nyholm PSR-17 factory.
	 *
	 * PSR-17 has different interfaces for each thing. Nyholm puts them all in one. Honestly, that makes sense, but the
	 * spec is the spec.
	 *
	 * @var Psr17Factory
	 */
	private static Psr17Factory $nyholmFactory;
	private static function default(): Psr17Factory {
		self::$nyholmFactory ??= new Psr17Factory();
		return self::$nyholmFactory;
	}

	/**
	 * Create a PSR-7 HTTP Request.
	 *
	 * @param HttpVerb                           $verb    HTTP method to use.
	 * @param string|UriInterface                $url     URI to retrieve.
	 * @param array                              $headers Any headers to add to the request.
	 * @param string|array|JsonSerializable|null $body    Body of the request. Arrays and objects will be
	 * 	                                                  serialized to JSON.
	 * @return RequestInterface
	 */
	public static function request(
		HttpVerb $verb,
		string|UriInterface $url,
		array $headers = [],
		string|array|JsonSerializable|null $body = null,
	): RequestInterface {
		$casedHeaders = \array_change_key_case($headers, CASE_LOWER);

		self::$internalRequest ??= self::default();
		$newRequest = self::$internalRequest->createRequest($verb->value, $url);

		if (isset($body)) {
			$parsedBody = \is_string($body) ? $body : (\json_encode($body) ?: '');
			$casedHeaders['content-type'] ??= 'application/json';

			self::$internalStream ??= self::default();
			$newRequest = $newRequest->withBody(self::$internalStream->createStream($parsedBody));
		}

		foreach ($headers as $key => $value) {
			$newRequest = $newRequest->withHeader($key, $value);
		}

		return $newRequest;
	}

	/**
	 * Create a PSR-7 HTTP Response.
	 *
	 * @param integer                                 $code    HTTP code for the response. Default 200 (OK).
	 * @param array                                   $headers Headers of the response.
	 * @param string|array|JsonSerializable|null|null $body    Response body in string or object format.
	 * @return RequestInterface
	 */
	public static function response(
		int $code = 200,
		array $headers = [],
		string|array|JsonSerializable|null $body = null,
	): ResponseInterface {
		$casedHeaders = \array_change_key_case($headers, CASE_LOWER);

		self::$internalResponse ??= self::default();
		$newResponse = self::$internalResponse->createResponse($code);

		if (isset($body)) {
			$parsedBody = \is_string($body) ? $body : (\json_encode($body) ?: '');
			$casedHeaders['content-type'] ??= 'application/json';

			self::$internalStream ??= self::default();
			$newResponse = $newResponse->withBody(self::$internalStream->createStream($parsedBody));
		}

		foreach ($headers as $key => $value) {
			$newResponse = $newResponse->withHeader($key, $value);
		}

		return $newResponse;
	}
}
