<?php

namespace Smolblog\Framework\Objects;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Smolblog\Test\TestCase;

final class HttpResponseTest extends TestCase {
	public function testItConformsToPsr7() {
		$response = new HttpResponse(code: 301, headers: ['Location' => 'https://smol.blog/']);

		$this->assertEquals(301, $response->getStatusCode());
		$this->assertEquals('Moved Permanently', $response->getReasonPhrase());
		$this->assertEquals('1.1', $response->getProtocolVersion());
		$this->assertEquals(['Location' => ['https://smol.blog/']], $response->getHeaders());
		$this->assertEquals(['https://smol.blog/'], $response->getHeader('location'));
		$this->assertEquals('https://smol.blog/', $response->getHeaderLine('location'));
		$this->assertInstanceOf(StreamInterface::class, $response->getBody());

		$this->assertInstanceOf(ResponseInterface::class, $response->withStatus(302));
		$this->assertInstanceOf(ResponseInterface::class, $response->withProtocolVersion('2.0'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withHeader('Bob', 'Larry'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withAddedHeader('Bob', 'Larry'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withoutHeader('Bob'));
		$this->assertInstanceOf(ResponseInterface::class, $response->withBody($this->createStub(StreamInterface::class)));
	}

	public function testItUsesAStringBodyVerbatim() {
		$response = new HttpResponse(body: 'one=two');

		$this->assertEquals('one=two', $response->getBody()->getContents());
	}

	public function testItFormatsAnArrayBodyIntoJson() {
		$body = ['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$response = new HttpResponse(body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $response->getBody()->getContents());
	}

	public function testItFormatsAnObjectBodyIntoJson() {
		$body = new class('two') extends Value {public function __construct(public readonly string $one) {}};
		$bodyJson = '{"one":"two"}';

		$response = new HttpResponse(body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $response->getBody()->getContents());
	}
}
