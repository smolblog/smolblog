<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use Psr\Http\Message\RequestInterface;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\TestCase;

final class MessageVerifierTest extends TestCase {
	private RequestInterface $postRequest;
	private Follow $postRequestBody;
	private string $publicKeyPem;

	protected function setUp(): void {
		$this->postRequestBody = new Follow(
			id: 'https://smol.blog/site/9abfcd19-fbc3-4ca7-bc92-506c1e599b36/activitypub/outbox/c06abf71-9084-4b92-9b29-0d15d7c0cbc7',
			actor: 'https://smol.blog/site/9abfcd19-fbc3-4ca7-bc92-506c1e599b36/activitypub/actor',
			object: 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor',
		);
		$this->postRequest = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/inbox',
			headers: [
				'date' => 'Tue, 14 Jan 2014 18:50:38 GMT',
				'digest' => 'SHA256=un7b964+PCST/h56Qvz9ejegIi6idGxvmwvrFP4DKcY=',
				'signature' => 'keyId="https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor",algorithm="rsa-sha256",headers="(request-target) host date digest content-type",signature="GJif5eYBq3eaQ+AOh5IRc5xzG0RoDwBGqQXJzpJkflvWblE9GHqx55Wb9WhmKUmuNLAkshoZ5DvF21zb0xo1iu/i/WJGRDpUo2TMxBaoyKR16WD+0uWkR+AX2+QSEK2SjZ8WwK7aHULPxWJMRnZ3E5TUREOhc5BZHid2UdhFUS5p/9KdmCcz503QNbKrqiUuXUxcZ75alWjJtKTn5x4E2JPGGUJ/5oyKJAsyrQeS3cabDy2wpWXI6//wTZPDMgrVab8Vvkhi34ErxKPXp96SZp9O4i1RzPTCNcbOIFaUyquxkv0rhrWTO/4II/jIlT5tLVg9rL7iqDqm08Nu+eWKvw=="',
			],
			body: $this->postRequestBody,
		);

		$this->publicKeyPem = <<<EOF
		-----BEGIN PUBLIC KEY-----
		MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAx6Nc41SpHD/SPoyE2V9h
		VVzUqLnuYVSb6+Uud28V/z885ySTvVpaLTNNmYk93xdR32T6x1aI5muy5HHF8X17
		E2Arh9p78MF0VaZnvPWQboMlMGBRY5dxsu2hznULSy+IHmiA+Tk8vs64Upg1zUsX
		fGuLoNrgw7/5drnjIgHCF8xnEuP2nycNN6yMnBiawwJX28CN2q9/JeDAjVncgw4g
		/j4mIEA4ZOfTkxau90Xju0WjWzwIah8jVER792lPI1hHKc/hE+MRidROZuG2GUgs
		NSJT81k9KexrqruiWsv1AaYZSWipjFU95HkUo+8E0jO3lrRU7QmMMwkaiLT3KnWK
		4wIDAQAB
		-----END PUBLIC KEY-----
		EOF;

		$this->subject = new MessageVerifier();
	}

	public function testItVerifiesAPostRequest() {
		$this->assertTrue($this->subject->verify(request: $this->postRequest, keyPem: $this->publicKeyPem));
	}

	public function testItVerifiesARealRequest() {
		$key = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzOQXkYZN7LoATFNQ3mm/\nSeBxRiI0BKpoRLSELCZR9U4GcZ2wHGTENvc++3h63vgIVXzgjWHSaMj1w+LvG3c4\nJV4FrOFGzrxtQvyFDUyNmihRU2+cxqLQiKuZbUxrKFtyA6hdmiCi8IX41UZiA9QB\nhmXMP0REj/OSth0FS8+o8iMN4kB0Qvq9JSrIkV0Lwv3jJs/LP9QLjX5fgJUVTbdP\npVus9AhLUJjZ3i/KIGehn9bbwg8PnEQOHuEO7lxO0YXetbv7+HQEV+jJAWY/5nJv\nFUTQTIOeGFa8FkdDgYwAxyXDzumrjY69DzXcXxkzro1spagh5wsRC08o3Cyi1mTm\n6QIDAQAB\n-----END PUBLIC KEY-----\n";
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/inbox',
			headers: [
				'User-Agent' => 'http.rb/5.1.1 (Mastodon/4.1.4; +https://activitypub.academy/)',
				'Content-Length' => '469',
				'Accept-Encoding' => 'gzip',
				'Content-Type' => 'application/activity+json',
				'Date' => 'Sun, 14 Jan 2024 21:58:31 GMT',
				'Digest' => 'SHA-256=drOoNpGTdRGU12Y9fJx0ebwNlD0ZJmD7x63da6cb4PI=',
				'Signature' => 'keyId="https://activitypub.academy/users/beguca_dedashul#main-key",algorithm="rsa-sha256",headers="(request-target) host date digest content-type",signature="UxXnyOOMRiKwyQD0xTlnA8rch+c732GymNf7/EBlu3z9l2WA971bgE8KM3QwWINE9R1iMRhx5xwtbViFQwxmxSDWKmvTpMej5QZNXmKcxxRSm3zD2DSimN8G6uGfWt31lPd0PqRR34VvD6Gm3IQDgtFDjSmEvJbsxedpQZaR2/Cs1Kqubs9ok/YzN4zMS2kvj0y+T71Db1KPPPEzvo6n+eOJRT44IofwS8z1qxA20h5DubZ2DXLqjKgsW7OVDJTZxzS7oGeubvisABzTEYkOvvDGa95b7vzSCjcmEvAOBdgbeBfHPOeWrmfW2fZyKKfrlyoYNX0fg3WBYU2mzTMQcw=="',
				'X-Forwarded-Host' => 'smol.blog',
				'X-Forwarded-Proto' => 'https',
			],
			body: '{"@context":"https://www.w3.org/ns/activitystreams","id":"https://activitypub.academy/users/beguca_dedashul#follows/1726/undo","type":"Undo","actor":"https://activitypub.academy/users/beguca_dedashul","object":{"id":"https://activitypub.academy/f3b123fe-c52a-4b51-a095-aa18043744e6","type":"Follow","actor":"https://activitypub.academy/users/beguca_dedashul","object":"https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/actor"}}',
		);

		$this->assertTrue($this->subject->verifyDigest($request));
		$this->assertEquals('https://activitypub.academy/users/beguca_dedashul#main-key', $this->subject->getKeyId($request));
		$this->assertTrue($this->subject->verify($request, $key));
	}
}
