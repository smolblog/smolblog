<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use DateTimeImmutable;
use Psr\Http\Message\RequestInterface;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\TestCase;

final class SignatureKitTest extends TestCase {
	private RequestInterface $getRequest;
	private RequestInterface $postRequest;
	private Follow $postRequestBody;

	protected function setUp(): void {
		$this->getRequest = new HttpRequest(
			verb: HttpVerb::GET,
			url: 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor',
			headers: ['Accept' => 'application/json'],
		);

		$this->postRequestBody = new Follow(
			id: 'https://smol.blog/site/9abfcd19-fbc3-4ca7-bc92-506c1e599b36/activitypub/outbox/c06abf71-9084-4b92-9b29-0d15d7c0cbc7',
			actor: 'https://smol.blog/site/9abfcd19-fbc3-4ca7-bc92-506c1e599b36/activitypub/actor',
			object: 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor',
		);
		$this->postRequest = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/inbox',
			body: $this->postRequestBody,
		);

		$this->subject = new class() {
			use SignatureKit {
				addDigest as public;
				addDate as public;
				generateSignatureSource as public;
				signatureHeaderFromParts as public;
				getSignatureHeaderParts as public;
			}
		};
	}

	public function testItAddsADigestOfTheRequestBody() {
		// Check the in-between steps.
		$this->assertEquals(
			expected: '{"@context":"https:\/\/www.w3.org\/ns\/activitystreams","type":"Follow","id":"https:\/\/smol.blog\/site\/9abfcd19-fbc3-4ca7-bc92-506c1e599b36\/activitypub\/outbox\/c06abf71-9084-4b92-9b29-0d15d7c0cbc7","actor":"https:\/\/smol.blog\/site\/9abfcd19-fbc3-4ca7-bc92-506c1e599b36\/activitypub\/actor","object":"https:\/\/smol.blog\/site\/c88e0395-cece-4037-8a2c-7be481a3c1fe\/activitypub\/actor"}',
			actual: json_encode($this->postRequestBody),
		);
		$this->assertEquals(
			expected: 'ba7edbf7ae3e3c2493fe1e7a42fcfd7a37a0222ea2746c6f9b0beb14fe0329c6',
			actual: hash('sha256', json_encode($this->postRequestBody)),
		);

		// This is the Base64-encoded SHA256 hash of the JSON-encoded object.
		$expected = 'un7b964+PCST/h56Qvz9ejegIi6idGxvmwvrFP4DKcY=';

		$this->assertEquals(
			"sha-256=$expected",
			$this->subject->addDigest($this->postRequest)->getHeaderLine('digest')
		);
	}

	public function testItDoesNotAddADigestIfTheRequestHasNoBody() {
		$this->assertEmpty(
			$this->subject->addDigest($this->getRequest)->getHeaderLine('digest')
		);
	}

	public function testItDoesNotAddADigestIfOneAlreadyExists() {
		$predigested = $this->postRequest->withAddedHeader('Digest', 'yum');

		$this->assertEquals(
			'yum',
			$this->subject->addDigest($predigested)->getHeaderLine('digest')
		);
	}

	public function testItAddsADateHeaderWithTheCurrentTime() {
		$expectedTime = new DateTimeImmutable();
		$actual = $this->subject->addDate($this->getRequest)->getHeaderLine('date');

		$this->assertNotEmpty($actual);

		$actualTime = new DateTimeImmutable($actual);
		$this->assertLessThanOrEqual(5, abs($expectedTime->getTimestamp() - $actualTime->getTimestamp()));
	}

	public function testItDoesNotAddADateIfOneAlreadyExists() {
		$predated = $this->getRequest->withAddedHeader('Date', 'future');

		$this->assertEquals(
			'future',
			$this->subject->addDigest($predated)->getHeaderLine('date')
		);
	}

	public function testItGeneratesASignatureSourceStringWithNormalHeaders() {
		$expected = "host: smol.blog\naccept: application/json";

		$this->assertEquals($expected, $this->subject->generateSignatureSource($this->getRequest, ['Host', 'Accept']));
	}

	public function testItGeneratesASignatureSourceStringWithTheRequestTarget() {
		$expected = "host: smol.blog\n(request-target): get /site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor" .
			"\naccept: application/json";

		$this->assertEquals($expected, $this->subject->generateSignatureSource(
			request: $this->getRequest,
			headers: ['Host', '(request-target)', 'Accept']
		));
	}

	public function testItGeneratesASignatureHeaderFromParts() {
		$parts = [
			'algorithm' => 'rsa-sha256',
			'keyId' => 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor#main-key',
			'signature' => '95cb5dcb-852c-47d1-a6bd-415c6d12f90e',
			'headers' => '(request-target) host content-type date',
		];

		$expected = 'algorithm="rsa-sha256",keyId="https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/' .
			'activitypub/actor#main-key",signature="95cb5dcb-852c-47d1-a6bd-415c6d12f90e",headers="(request-target)' .
			' host content-type date"';

		$this->assertEquals($expected, $this->subject->signatureHeaderFromParts($parts));
	}

	public function testItSplitsASignatureHeader() {
		$expected = [
			'algorithm' => 'rsa-sha256',
			'keyId' => 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor#main-key',
			'signature' => '95cb5dcb-852c-47d1-a6bd-415c6d12f90e',
			'headers' => '(request-target) host content-type date',
		];

		$actual = 'algorithm="rsa-sha256",keyId="https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/' .
			'activitypub/actor#main-key",signature="95cb5dcb-852c-47d1-a6bd-415c6d12f90e",headers="(request-target)' .
			' host content-type date"';

		$this->assertEquals($expected, $this->subject->getSignatureHeaderParts($actual));
	}

	public function testItReturnsAnEmptyArrayIfSignatureHeaderIsMalformed() {
		$this->assertEquals([], $this->subject->getSignatureHeaderParts(''));
		$this->assertEquals([], $this->subject->getSignatureHeaderParts('something: wrong, something: else'));
	}
}
