<?php

namespace Smolblog\Foundation\Value\Http;

use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;
use Smolblog\Test\TestCase;

#[CoversClass(HttpRequest::class)]
final class HttpRequestTest extends TestCase {
	public function testItConformsToPsr7() {
		$request = new HttpRequest(verb: HttpVerb::GET, url: 'https://smol.blog/hello');

		$this->assertEquals('/hello', $request->getRequestTarget());
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('https://smol.blog/hello', $request->getUri());
		$this->assertEquals('1.1', $request->getProtocolVersion());
		$this->assertEquals(['Host' => ['smol.blog']], $request->getHeaders());
		$this->assertEquals(['smol.blog'], $request->getHeader('host'));
		$this->assertTrue($request->hasHeader('Host'));
		$this->assertEquals('smol.blog', $request->getHeaderLine('host'));
		$this->assertInstanceOf(StreamInterface::class, $request->getBody());

		$this->assertInstanceOf(RequestInterface::class, $request->withRequestTarget('/'));
		$this->assertInstanceOf(RequestInterface::class, $request->withMethod('POST'));
		$this->assertInstanceOf(RequestInterface::class, $request->withUri($this->createStub(UriInterface::class)));
		$this->assertInstanceOf(RequestInterface::class, $request->withProtocolVersion('2.0'));
		$this->assertInstanceOf(RequestInterface::class, $request->withHeader('Bob', 'Larry'));
		$this->assertInstanceOf(RequestInterface::class, $request->withAddedHeader('Bob', 'Larry'));
		$this->assertInstanceOf(RequestInterface::class, $request->withoutHeader('Bob'));
		$this->assertInstanceOf(RequestInterface::class, $request->withBody($this->createStub(StreamInterface::class)));
	}

	public function testItUsesAStringBodyVerbatim() {
		$request = new HttpRequest(verb: HttpVerb::GET, url: 'https://smol.blog/hello', body: 'one=two');

		$this->assertEquals('one=two', $request->getBody()->getContents());
	}

	public function testItFormatsAnArrayBodyIntoJson() {
		$body = ['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$request = new HttpRequest(verb: HttpVerb::GET, url: 'https://smol.blog/hello', body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $request->getBody()->getContents());
	}

	public function testItFormatsAnObjectBodyIntoJson() {
		$body = new readonly class('two') extends Value implements SerializableValue {
			use SerializableValueKit;
			public function __construct(public readonly string $one) {}
		};
		$bodyJson = '{"one":"two"}';

		$request = new HttpRequest(verb: HttpVerb::GET, url: 'https://smol.blog/hello', body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $request->getBody()->getContents());
	}
}
