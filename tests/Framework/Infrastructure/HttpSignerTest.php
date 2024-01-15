<?php

namespace Smolblog\Framework\Infrastructure;

use DateTimeImmutable;
use DateTimeInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Framework\Objects\Keypair;
use Smolblog\Test\TestCase;

final class HttpSignerTest extends TestCase {
	private RequestInterface $request;
	private Keypair $keypair;
	private string $signature;
	private LoggerInterface $logger;

	/**
	 * Test values from https://dinochiesa.github.io/httpsig/
	 */
	public function setUp(): void {
		$this->request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/inbox',
			headers: [
				'Host' => 'smol.blog, smol.blog',
				'User-Agent' => 'http.rb/5.1.1 (Mastodon/4.1.4; +https://activitypub.academy/)',
				'Content-Length' => '309',
				'Accept-Encoding' => 'gzip',
				'Content-Type' => 'application/activity+json',
				'Date' => 'Thu, 11 Jan 2024 23:37:52 GMT',
				'Digest' => 'SHA-256=yxwaFwNXclumUjv8VZ6d+C8OS6uWi4dBxHc9LnYC2uU=',
				'Signature' => 'keyId="https://activitypub.academy/users/beguca_dedashul#main-key",algorithm="rsa-sha256",headers="(request-target) host date digest content-type",signature="Saa8Y6O037bjYCjvW49GM6yPqwWSPsdlXYG8WdD3KG0AzM3ankL2Vvgp/Ofq0ykidvN6DzoYgInza68/QfJrhv6jxjkdkOsyRr3gHBvIK8OUpBfTjsFemUBmYJQx8Klocc+MEObjh9Txs/XrTjPQI4fcnBd3/1095uzMOInlTcrXziGF3io5Wkdhj6cr/0dOEK+d0ItiUhSS6JjkXjAcGXgCyZFy/04hqOn0FsM3awz5OoMm6PbDrYBywlDv4QjqVw1mpgczmYdrfRW3EcMwlXaN1hnlA3kWmyHeE7QwyoFw27pkbIJfzB2AOakQdBcLA5FWJyN2r8KBWaT10PBeJA=="',
				'X-Forwarded-Host' => 'smol.blog',
				'X-Forwarded-Proto' => 'https',
			],
			body: '{"@context":"https://www.w3.org/ns/activitystreams","id":"https://activitypub.academy/f3b123fe-c52a-4b51-a095-aa18043744e6","type":"Follow","actor":"https://activitypub.academy/users/beguca_dedashul","object":"https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/actor"}',
		);

		$this->keypair = new Keypair(
			publicKey: "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzOQXkYZN7LoATFNQ3mm/\nSeBxRiI0BKpoRLSELCZR9U4GcZ2wHGTENvc++3h63vgIVXzgjWHSaMj1w+LvG3c4\nJV4FrOFGzrxtQvyFDUyNmihRU2+cxqLQiKuZbUxrKFtyA6hdmiCi8IX41UZiA9QB\nhmXMP0REj/OSth0FS8+o8iMN4kB0Qvq9JSrIkV0Lwv3jJs/LP9QLjX5fgJUVTbdP\npVus9AhLUJjZ3i/KIGehn9bbwg8PnEQOHuEO7lxO0YXetbv7+HQEV+jJAWY/5nJv\nFUTQTIOeGFa8FkdDgYwAxyXDzumrjY69DzXcXxkzro1spagh5wsRC08o3Cyi1mTm\n6QIDAQAB\n-----END PUBLIC KEY-----\n",
			privateKey: 'invalid',
		);

		// $this->signature = 'keyId="abcdefg-123", algorithm="rsa-sha256", headers="x-request-id tpp-redirect-uri digest psu-id", signature="H2hAPwRXjVS4ikp/FnqPaJHLNnuLuLmMv0vEsrozPO7CfDu/zSaH0GJU6nKimKtrgkFSwttNd+KoRLQv/OHSk6OICXscc934BiviwrzMBdk3owLZQGllsoDiyOEtlgHsqZsVKKCtDfdY6LpopdZOlzOE1kBrRIlTMxxEYeefWgY+RUH6zPEz9F5cdCYXRDuYZ+NYtKtKzdao0kXriNZeTvj9ls4CNWjRfEEowQF+l+r1x+zi++iO0OmQmkRIDRmkv1YIrYtK6B0BJnngLR358qqORqr9m8qmXPTXA/3GqbmK8INzXuXHf9Zpt7Vs2bNVfqe+Zew5P6doxVplowUkeA=="';

		$this->logger = $this->createMock(LoggerInterface::class);
		$this->subject = new HttpSigner();
	}

	public function testItValidatesAGivenSignature() {
		$signedRequest = $this->request;

		$service = new HttpSigner();
		$this->assertTrue($service->verify(
			request: $signedRequest,
			keyId: 'https://activitypub.academy/users/beguca_dedashul#main-key',
			keyPem: $this->keypair->publicKey
		));
	}

	public function notestItAddsAValidSignatureToAHttpRequest() {
		$request = (new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/activitypub/inbox',
			body: ['thing' => 'happened']
		))->withAddedHeader('Date', 'Sat, 30 Apr 2016 17:52:13 GMT');


		$service = new HttpSigner();
		$request = $service->sign(
			request: $request,
			keyId: 'https://smolblog.localhost/api/site/22523c80-32f6-45ff-a5bd-a623ebc1d0ac/activitypub/actor#publicKey',
			keyPem: $this->keypair->privateKey
		);

		$this->assertEquals(
			'keyId="https://smolblog.localhost/api/site/22523c80-32f6-45ff-a5bd-a623ebc1d0ac/activitypub/actor#publicKey",algorithm="rsa-sha256",headers="(request-target) date host digest",signature="M82XytRCoUzxMk95oE5P9Hcn3HXvjCG3GyT8/WLSmyodvWEvCWNdGUB+9Xs4jcXAg/GoZXts9TGs1IiLZUL1OQHe/Uarm+V9Jiw+Tnlu8gdjgnhW8NNGbe6XsH75dzrOLAECUN/DzBmLY3QxNlHKsZrZ2VLjvIaA1gCdRdiVUQ7NnC31Z5tVNLF55XOOzIksRYdR9hmRXUC+MNVgub0z8TsIYzuv1kCGjk8rSahlFcfvPOUWAuFooWUbCEsbweuMLk2d2E/MLwRvZhKM4nSJfhue4MJkoLghRXjqchqHcdAwmGzZtdxI6kSME0SpP/+FxTkSCMZYJZRVaIkrzfQQiQ=="',
			$request->getHeaderLine('Signature')
		);

		$this->assertTrue($service->verify(
			request: $request,
			keyId: 'https://smolblog.localhost/api/site/22523c80-32f6-45ff-a5bd-a623ebc1d0ac/activitypub/actor#publicKey',
			keyPem: $this->keypair->publicKey
		));
	}

	public function notestItAddsADateHeaderIfNoneExists() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/activitypub/inbox',
			body: ['thing' => 'happened']
		);

		$service = new HttpSigner();
		$now = new DateTimeImmutable();
		$request = $service->sign(
			request: $request,
			keyId: 'https://smolblog.localhost/api/site/0c2f2fe8-8098-4868-a6f7-7a37dc679662/activitypub/actor#publicKey',
			keyPem: $this->keypair->privateKey
		);

		$reqTime = new DateTimeImmutable($request->getHeaderLine('Date'));
		$timeDiff = abs($reqTime->getTimestamp() - $now->getTimestamp());
		$this->assertLessThanOrEqual(30, $timeDiff);
	}
}
