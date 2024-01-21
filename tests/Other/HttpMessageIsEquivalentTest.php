<?php

namespace Smolblog\Test;

use InvalidArgumentException;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\HttpResponse;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\Kits\HttpMessageComparisonTestKit;

final class HttpMessageIsEquivalentTest extends TestCase {
	use HttpMessageComparisonTestKit;

	public function testEquivalentHttpMessagesPass() {
		$messageOne = new HttpResponse(
			code: 451,
			headers: ['Link' => '<https://spqr.example.org/legislatione>; rel="blocked-by"'],
			body: ['code' => '451', 'blockedBy' => 'Copyright, LLC'],
		);
		$messageTwo = new HttpResponse(
			code: 451,
			headers: ['Link' => '<https://spqr.example.org/legislatione>; rel="blocked-by"'],
			body: ['code' => '451', 'blockedBy' => 'Copyright, LLC'],
		);

		$this->assertThat($messageTwo, $this->httpMessageEqualTo($messageOne));
	}

	public function testDifferentHttpMessagesFail() {
		$messageOne = new HttpResponse(
			code: 451,
			headers: ['Link' => '<https://spqr.example.org/legislatione>; rel="blocked-by"'],
			body: ['code' => '451', 'blockedBy' => 'Copyright, LLC'],
		);
		$messageTwo = new HttpRequest(verb: HttpVerb::GET, url: 'https://smol.blog/');

		$this->assertThat($messageTwo, $this->logicalNot($this->httpMessageEqualTo($messageOne)));
	}

	public function testNotPassingAnHttpMessagetWillThrowException() {
		$this->expectException(InvalidArgumentException::class);

		$messageOne = new HttpResponse(
			code: 451,
			headers: ['Link' => '<https://spqr.example.org/legislatione>; rel="blocked-by"'],
			body: ['code' => '451', 'blockedBy' => 'Copyright, LLC'],
		);
		$messageTwo = 'Hello!';

		$this->assertThat($messageTwo, $this->httpMessageEqualTo($messageOne));
	}
}
