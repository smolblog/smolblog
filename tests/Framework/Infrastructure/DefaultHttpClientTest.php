<?php

namespace Smolblog\Framework\Infrastructure;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Test\TestCase;

final class DefaultHttpClientTest extends TestCase {
	private Client $guzzleClient;

	public function setUp(): void {
		$this->guzzleClient = $this->createMock(Client::class);
	}

	public function testItCreatesARequestWithMinimalParameters() {
		$url = 'https://smol.blog/';
		$response = $this->createStub(ResponseInterface::class);

		$this->guzzleClient->expects($this->once())->method('request')->with(
			$this->equalTo('GET'), // Method
			$this->equalTo($url), // URI
			$this->equalTo([]), // Options
		)->willReturn($response);

		$service = new DefaultHttpClient($this->guzzleClient);
		$this->assertEquals($response, $service->request(url: $url));
	}

	public function testCustomHeadersCanBeSet() {
		$this->assertTrue(false);
	}

	public function testAnObjectBodyIsPassedAsJson() {
		$this->assertTrue(false);
	}

	public function testAnArrayBodyIsPassedAsJson() {
		$this->assertTrue(false);
	}

	public function testAStringBodyIsPassedVerbatim() {
		$this->assertTrue(false);
	}
}
