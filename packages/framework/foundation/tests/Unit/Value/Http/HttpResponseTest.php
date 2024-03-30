<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Http\HttpResponse;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

describe('HttpResponse', function() {
	it('conforms to PSR-7', function() {
		$response = new HttpResponse(code: 301, headers: ['Location' => 'https://smol.blog/']);
		expect($response)->toBeInstanceOf(ResponseInterface::class);

		expect($response->getStatusCode())->toBe(301);
		expect($response->getReasonPhrase())->toBe('Moved Permanently');
		expect($response->getProtocolVersion())->toBe('1.1');
		expect($response->getHeaders())->toBe(['location' => ['https://smol.blog/']]);
		expect($response->getHeader('location'))->toBe(['https://smol.blog/']);
		expect($response->getHeaderLine('location'))->toBe('https://smol.blog/');
		expect($response->getBody())->toBeInstanceOf(StreamInterface::class);
		expect($response->hasHeader('location'))->toBeTrue();

		$this->assertInstanceOf(ResponseInterface::class, $response->withStatus(302));
		$this->assertInstanceOf(ResponseInterface::class, $response->withProtocolVersion('2.0'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withHeader('Bob', 'Larry'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withAddedHeader('Bob', 'Larry'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withoutHeader('Bob'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withBody(Mockery::mock(StreamInterface::class)));
	});
});

describe('HttpResponse::__construct', function() {
	it('uses a string body verbatim', function() {
		$request = new HttpResponse(body: 'one=two');

		expect($request->getBody()->__toString())->toBe('one=two');
	});

	it('formats an array body into JSON', function() {
		$body = ['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$request = new HttpResponse(body: $body);

		expect($request->getBody()->__toString())->toBe($bodyJson);
		expect($request->getHeaderLine('content-type'))->toBe('application/json');
	});

	it('formats an object body into JSON', function() {
		$body = new readonly class('two') extends Value implements SerializableValue {
			use SerializableValueKit;
			public function __construct(public readonly string $one) {}
		};
		$bodyJson = '{"one":"two"}';

		$request = new HttpResponse(body: $body);

		expect($request->getBody()->__toString())->toBe($bodyJson);
		expect($request->getHeaderLine('content-type'))->toBe('application/json');
	});

	it('does not set the content-type header if one is provided', function() {
		$request = new HttpResponse(
			headers: ['content-type' => 'application/activity+json'],
			body: ['type' => 'Follow'],
		);

		expect($request->getBody()->__toString())->toBe('{"type":"Follow"}');
		expect($request->getHeaderLine('content-type'))->toBe('application/activity+json');
	});
});
