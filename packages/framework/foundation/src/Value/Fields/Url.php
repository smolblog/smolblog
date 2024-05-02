<?php

namespace Smolblog\Foundation\Value\Fields;

use GuzzleHttp\Psr7\Exception\MalformedUriException;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Field;
use Smolblog\Foundation\Value\Traits\FieldKit;

readonly class Url extends Value implements Field, UriInterface {
	use FieldKit;

	private UriInterface $internal;

	/**
	 * Create the field from a URL string.
	 *
	 * @throws InvalidValueProperties If the URL is not valid.
	 *
	 * @param string $url Valid URL.
	 */
	public function __construct(string $url) {
		try {
			$this->internal = new Uri($url);
		} catch (MalformedUriException $e) {
			throw new InvalidValueProperties($e->getMessage());
		}
	}


	public function toString(): string {
		return strval($this->internal);
	}

	public static function fromString(string $string): static {
		return new static($string);
	}


	public function getAuthority() {
		return $this->internal->getAuthority();
	}

	public function getFragment() {
		return $this->internal->getFragment();
	}

	public function getHost() {
		return $this->internal->getHost();
	}

	public function getPath() {
		return $this->internal->getPath();
	}

	public function getPort() {
		return $this->internal->getPort();
	}

	public function getQuery() {
		return $this->internal->getQuery();
	}

	public function getScheme() {
		return $this->internal->getScheme();
	}

	public function getUserInfo() {
		return $this->internal->getUserInfo();
	}

	public function withFragment(string $fragment): UriInterface {
		return $this->internal->withFragment($fragment);
	}

	public function withHost(string $host): UriInterface {
		return $this->internal->withHost($host);
	}

	public function withPath(string $path): UriInterface {
		return $this->internal->withPath($path);
	}

	public function withPort(int|null $port): UriInterface {
		return $this->internal->withPort($port);
	}

	public function withQuery(string $query): UriInterface {
		return $this->internal->withQuery($query);
	}

	public function withScheme(string $scheme): UriInterface {
		return $this->internal->withScheme($scheme);
	}

	public function withUserInfo(string $user, string|null $password = null): UriInterface {
		return $this->internal->withUserInfo(user: $user, password: $password);
	}
}
