<?php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Value\Http\HttpRequest;
use Smolblog\Framework\Foundation\Value\Http\HttpVerb;
use Smolblog\Framework\Foundation\Value\Traits\SerializableValue;
use Smolblog\Framework\Foundation\Value\Traits\SerializableValueKit;

describe('HttpRequest', function () {
	it('conforms to PSR-7', function() {
		$request = new HttpRequest(verb: HttpVerb::GET, url: 'https://smol.blog/hello');
		expect($request)->toBeInstanceOf(RequestInterface::class);

		expect($request->getRequestTarget())->toBe('/hello');
		expect($request->getMethod())->toBe('GET');
		expect($request->getUri()->__toString())->toBe('https://smol.blog/hello');
		expect($request->getProtocolVersion())->toBe('1.1');
		expect($request->getHeaders())->toBe(['Host' => ['smol.blog']]);
		expect($request->getHeader('host'))->toBe(['smol.blog']);
		expect($request->getHeaderLine('host'))->toBe('smol.blog');
		expect($request->hasHeader('host'))->toBeTrue();

		$mockUri = new GuzzleHttp\Psr7\Uri('https://smol.blog/goodbye');

		expect($request->getBody())->toBeInstanceOf(StreamInterface::class);
		expect($request->withRequestTarget('/'))->toBeInstanceOf(RequestInterface::class);
		expect($request->withMethod('POST'))->toBeInstanceOf(RequestInterface::class);
		expect($request->withUri($mockUri))->toBeInstanceOf(RequestInterface::class);
		expect($request->withProtocolVersion('2.0'))->toBeInstanceOf(RequestInterface::class);
		expect($request->withHeader('Bob', 'Larry'))->toBeInstanceOf(RequestInterface::class);
		expect($request->withAddedHeader('Bob', 'Larry'))->toBeInstanceOf(RequestInterface::class);
		expect($request->withoutHeader('Bob'))->toBeInstanceOf(RequestInterface::class);
		expect($request->withBody(Mockery::mock(StreamInterface::class)))->toBeInstanceOf(RequestInterface::class);
	});
});

describe('HttpRequest::__construct', function() {
	it('uses a string body verbatim', function() {
		$request = new HttpRequest(verb: HttpVerb::POST, url: 'https://smol.blog/hello', body: 'one=two');

		expect($request->getBody()->__toString())->toBe('one=two');
	});

	it('formats an array body into JSON', function() {
		$body = ['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$request = new HttpRequest(verb: HttpVerb::POST, url: 'https://smol.blog/hello', body: $body);

		expect($request->getBody()->__toString())->toBe($bodyJson);
		expect($request->getHeaderLine('content-type'))->toBe('application/json');
	});

	it('formats an object body into JSON', function() {
		$body = new readonly class('two') extends Value implements SerializableValue {
			use SerializableValueKit;
			public function __construct(public readonly string $one) {}
		};
		$bodyJson = '{"one":"two"}';

		$request = new HttpRequest(verb: HttpVerb::POST, url: 'https://smol.blog/hello', body: $body);

		expect($request->getBody()->__toString())->toBe($bodyJson);
		expect($request->getHeaderLine('content-type'))->toBe('application/json');
	});

	it('does not set the content-type header if one is provided', function() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/hello',
			headers: ['content-type' => 'application/activity+json'],
			body: ['type' => 'Follow'],
		);

		expect($request->getBody()->__toString())->toBe('{"type":"Follow"}');
		expect($request->getHeaderLine('content-type'))->toBe('application/activity+json');
	});
});
