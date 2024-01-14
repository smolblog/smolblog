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
}
