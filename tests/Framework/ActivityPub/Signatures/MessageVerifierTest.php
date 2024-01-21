<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use Psr\Http\Message\RequestInterface;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\TestCase;

/**
 * Some tests use the spec from
 * https://codeberg.org/helge/fediverse-features/src/branch/main/fedi/http_signatures.feature
 */
final class MessageVerifierTest extends TestCase {
	private string $publicKeyPem;
	private string $privateKeyPem;

	protected function setUp(): void {
		$this->publicKeyPem = <<<EOF
		-----BEGIN PUBLIC KEY-----
		MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA15vhFdK272bbGDzLtypo
		4Nn8mNFY3YSLnrAOX4zZKkNmWmgypgDP8qXjNiVsBf8f+Yk3tHDs58LMf8QDSP09
		A+zrlWBHN1rLELn0JBgqT9xj8WSobDIjOjFBAy4FKUko7k/IsYwTl/Vnx1tykhPR
		1UzbaNqN1yQSy0zGbIce/Xhqlzm6u+twyuHVCtbGPcPh7for5o0avKdMwhAXpWMr
		Noc9L2L/9h3UgoePgAvCE6HTPXEBPesUBlTULcRxMXIZJ7P6eMkb2pGUCDlVF4EN
		vcxZAG8Pb7HQp9nzVwK4OXZclKsH1YK0G8oBGTxnroBtq7cJbrJvqNMNOO5Yg3cu
		6QIDAQAB
		-----END PUBLIC KEY-----
		EOF;

		$this->privateKeyPem = <<<EOF
		-----BEGIN PRIVATE KEY-----
		MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDXm+EV0rbvZtsY
		PMu3Kmjg2fyY0VjdhIuesA5fjNkqQ2ZaaDKmAM/ypeM2JWwF/x/5iTe0cOznwsx/
		xANI/T0D7OuVYEc3WssQufQkGCpP3GPxZKhsMiM6MUEDLgUpSSjuT8ixjBOX9WfH
		W3KSE9HVTNto2o3XJBLLTMZshx79eGqXObq763DK4dUK1sY9w+Ht+ivmjRq8p0zC
		EBelYys2hz0vYv/2HdSCh4+AC8ITodM9cQE96xQGVNQtxHExchkns/p4yRvakZQI
		OVUXgQ29zFkAbw9vsdCn2fNXArg5dlyUqwfVgrQbygEZPGeugG2rtwlusm+o0w04
		7liDdy7pAgMBAAECggEAS9TtY4mLAcSBRpMLa06lOIAy0WTABpk5qgRt6blWIAE4
		nI+NUMl0WflyYnbi+XDzxAY462PUTuc6ma1NIny+2wSXDyCfq55pUWa1sYQ2TYRM
		OniWrAcuUKdGIGItOooatUamZZvIwGd1qq5FK4+A+65edRB5VrO/UHWeTElx4t+z
		0SUyfbpeCHvuEEK3OyY464V4ZW/D/zAAONaCF3n+FY9uBS0+9LEme0xvaBq24oF9
		zmbFJ5DjFSRpNVouGRmO03Uh9+uNrTYkcylDNgQaFCt7WcQO/3lCs+dqNf2psIht
		PCbLRqfXQSjQ6gQbnYPGmIFesVVwJpSyMXpFMcgISQKBgQDfBsmCBN0pFib4vaMf
		1KSLVxg7DFFiELf1D0ok4rSa7H5eo5flNND9xu+ESWQpcq9J0PVGZqfX7pen9EJV
		HmNPMYsAejTqHM/1bg3TdW3A4Xn0I2ShlZlNK0AzLYZRIBF0ZiCWunZBt8xjSLTm
		BGHZxvho1TZIffuudcxP5olY3wKBgQD3fFjQQt/bw/2JYUBFvjPiVOJ5j5MzaGLx
		gtpDMUFTuG/DPXUvTWp6xIkQj4xO5D9SfkTqBEPOGH/zc7CVoTMsh5vHDJ9fKE/l
		FynQaCzBOrU+zKeHvBscS91orF0nNrD3JYtb4GUe1oSZ/BlbwHjZMZQOAssaLDs1
		Bdq5SpLJNwKBgDXiU+k/95cnrP7IApN8Ms0fm9EYZslEtM1WhllnFK+hl96Rs+9C
		1YOa/t99Q9/nv4YcIEaEIuU+1hFUKHqcPu4xUB4raIFvuKbZkimW44+IaoibzIJl
		vIYyfu5ef2c2UkFHM3R3VH8IQy9xr5MrV+Df+8CIUvcsyRQbjeN4FZMNAoGBAOKB
		NxPcsN+FYC11CYsLSpcyE1koc5PQTQY3OaXXla+XFQr+25qgYvzblYrHpqWptt68
		XDxGDPy6ZZieYJaBw8FUl9k0j0RbM8w7R/TK83MiVTGVwxqyqalbMdgUMOmr34lD
		HmnHVSVFNnVsSpUz8ibufk/YdKSOqN2dbxK40uE/AoGBALGrR5FU2u1vcsUj1IvT
		epfA5+8kiQ5MdUWU+E7ORM+SRlnicsdS/IPT4KREBck/+GvXY/XZdMYT+T3a4o3P
		R4O3/2egqTchuPkwfSAy7L8jt2GNzFvxmcrvpKYAZzjh1KCrZ15OYr7ZhlEWs6MQ
		RbdDq36O45uplOe0heeOsPhj
		-----END PRIVATE KEY-----
		EOF;

		$this->subject = new MessageVerifier();
	}

	public function testItVerifiesTheGetRequestFromTheFediSpec() {
		$request = new HttpRequest(
			verb: HttpVerb::GET,
			url: 'https://myhost.example/path/to/resource',
			headers: [
				'date' => 'Wed, 15 Mar 2023 17:28:15 GMT',
				'signature' => 'keyId="https://remote.example/actor#key",algorithm="rsa-sha256",headers="(request-target) host date",signature="hUW2jMUkhiKTmAoqgq7CDz0l4nYiulbVNZflKu0Rxs34FyBs0zkBKLZLUnR35ptOvsZA7hyFOZbmK9VTw2VnoCvUYDPUb5VyO3MRpLv0pfXNExQEWuBMEcdvXTo30A0WIDSL95u7a6sQREjKKHD5+edW85WhhkqhPMtGpHe95cMItIBv6K5gACrsOYf8TyhtYqBxz8Et0iwoHnMzMCAHN4C+0nsGjqIfxlSqUSMrptjjov3EBEnVii9SEaWCH8AUE9kfh3FeZkT+v9eIDZdhj4+opnJlb9q2+7m/7YH0lxaXmqro0fhRFTd832wY/81LULix/pWTOmuJthpUF9w6jw=="',
			],
		);

		$this->assertTrue($this->subject->verifySignature($request, $this->publicKeyPem));
	}

	public function testItVerifiesThePostRequestFromTheFediSpec() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://myhost.example/path/to/resource',
			headers: [
				'date' => 'Wed, 15 Mar 2023 17:28:15 GMT',
				'digest' => 'sha-256=VOV9b4OFUAdF0mGBVK62bE+PT3t0UtTEfq7hNT3zv9U=',
				'signature' => 'keyId="https://remote.example/actor#key",algorithm="rsa-sha256",headers="(request-target) host date",signature=keyId="https://remote.example/actor#key",algorithm="rsa-sha256",headers="(request-target) host date digest",signature="gat6knmRUKkFUT2Pz66fjPXfhmUPx8peccozPFeGDrOixfjgmmyvaVgknnINlC7k9xE67//rVy5On7esftVuSzL4z39tbFd9WsPvQ+nDuFynD1q8vPRt4BLNDr4WbxG+jLPQJBPoHReaZqPe/nPSzpfTU9qNKpLWx78yoYkW1ag71on74M8K/X7x6DNq0TBJQqxsADsfyiOeDftPv3AonBZOQBYP9fucBKmCurRNXyn3jdaYGW+cDlMQECBI78yd32VKIAJUZVHbVn7l7qcNLfywwetMfQbdoJtHrpt8JT0cbZSpe7D4Rn6eNBmTr5DVIW+V0M4TMhoWwAzAv6Ka/w=="',
			],
			body: '{"cows": "are the best"}',
		);

		$this->assertTrue($this->subject->verifyDigest($request));
		$this->assertTrue($this->subject->verifySignature($request, $this->publicKeyPem));
	}

	public function testItVerifiesARealRequest() {
		$key = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsONgyQbTwQRBCPL6jtQy\nQfs9Zz8M+XcdTdnMxuoCR88gD3ikdcjwtYH3qWtR2O8wA6Z8PRouNr5quom7pAUt\nixMHdeQIw+aA/ja2ndO7aGZfjcZ7xTGDhE/mwrh6IpH4rCXqs5IFa+h80j3PS2Ab\n4oRGbORL+uI9qjHCzwFUCxjIpqklzwA6lkqQEgvaCgnc6RNrzC075wM7JWqsLmOq\nfecNaj+H624OseeO1GApU3EOOL+qZX+13m/sQanZ6GvDZZhlh81tO6/sqMOOWiZA\nEL8+ZXoGkF7B9deAyHRdm17zFolzydlFgl/X/CX6ZNcREUAsbibP8QoO5+4HmUEK\nowIDAQAB\n-----END PUBLIC KEY-----";
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/inbox',
			headers: [
				'User-Agent' => 'http.rb/5.1.1 (Mastodon/4.1.4; +https://activitypub.academy/)',
				'Content-Length' => '469',
				'Accept-Encoding' => 'gzip',
				'Content-Type' => 'application/activity+json',
				'Date' => 'Tue, 16 Jan 2024 02:22:36 GMT',
				'Digest' => 'SHA-256=2iM6tHWBVUf+dXrjvU5dFwPd0kvPeW22Bzxv85MYyqA=',
				'Signature' => 'keyId="https://activitypub.academy/users/anules_vaabis#main-key",algorithm="rsa-sha256",headers="(request-target) host date digest content-type",signature="eb170loU3zsZn/gbze1xorpEsCtroyp/vrVEBOj7P2XaV1X89vbFyAnFaod3d1F9A+J48aB0fN067yBDcp/wVVDwnVKdOLknrl37zd7RlF7R+TWEWesMJqLOmRKHlbK8eNXOlMbMuionMlqr0VuwjcqyXXd9uWseHpEHjnoLNn2CqGILCzQfAsTDBiiYiBADOLuxR/PfsuJzsHmKi7++os9QrjJ5Q9GI3mkn3YfEW5HTfc136Z4W3EEp6zMxTGBZ7YFJKtmfZFVCRwQI31dkv4a5jpPPZE+fGbCipFe59+v0WKgPGlW02YOwDZMeJRT1omFqijYqepHrFyT9HJUfKA=="',
				'X-Forwarded-Host' => 'smol.blog',
				'X-Forwarded-Proto' => 'https',
			],
			body: '{"@context":"https://www.w3.org/ns/activitystreams","id":"https://activitypub.academy/users/anules_vaabis#follows/1749/undo","type":"Undo","actor":"https://activitypub.academy/users/anules_vaabis","object":{"id":"https://activitypub.academy/f636a5fd-4629-47c8-b134-45dd4945eda1","type":"Follow","actor":"https://activitypub.academy/users/anules_vaabis","object":"https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/actor"}}',
		);

		$this->assertTrue($this->subject->verifyDigest($request));
		$this->assertEquals('https://activitypub.academy/users/anules_vaabis#main-key', $this->subject->getKeyId($request));
		$this->assertTrue($this->subject->verifySignature($request, $key));
	}

	public function testItReturnsFalseIfThereIsNoSignature() {
		$request = new HttpRequest(
			verb: HttpVerb::GET,
			url: 'https://smol.blog/something',
		);

		$this->assertFalse($this->subject->verifySignature($request, $this->publicKeyPem));
	}

	public function testItReturnsTrueIfThereIsNoBodyAndNoDigest() {
		$request = new HttpRequest(
			verb: HttpVerb::GET,
			url: 'https://smol.blog/something',
		);

		$this->assertTrue($this->subject->verifyDigest($request, $this->publicKeyPem));
	}

	public function testItReturnsFalseIfTheDigestHeaderHasNoEqualsSign() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://myhost.example/path/to/resource',
			headers: [
				'date' => 'Wed, 15 Mar 2023 17:28:15 GMT',
				'digest' => 'VOV9b4OFUAdF0mGBVK62bE+PT3t0UtTEfq7hNT3zv9U',
			],
			body: '{"cows": "are the best"}',
		);

		$this->assertFalse($this->subject->verifyDigest($request));
	}

	public function testItRejectsMessagesOlderThanTwentyFourHours() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://myhost.example/path/to/resource',
			headers: [
				'date' => 'Wed, 15 Mar 2023 17:28:15 GMT',
				'digest' => 'sha-256=VOV9b4OFUAdF0mGBVK62bE+PT3t0UtTEfq7hNT3zv9U=',
				'signature' => 'keyId="https://remote.example/actor#key",algorithm="rsa-sha256",headers="(request-target) host date",signature=keyId="https://remote.example/actor#key",algorithm="rsa-sha256",headers="(request-target) host date digest",signature="gat6knmRUKkFUT2Pz66fjPXfhmUPx8peccozPFeGDrOixfjgmmyvaVgknnINlC7k9xE67//rVy5On7esftVuSzL4z39tbFd9WsPvQ+nDuFynD1q8vPRt4BLNDr4WbxG+jLPQJBPoHReaZqPe/nPSzpfTU9qNKpLWx78yoYkW1ag71on74M8K/X7x6DNq0TBJQqxsADsfyiOeDftPv3AonBZOQBYP9fucBKmCurRNXyn3jdaYGW+cDlMQECBI78yd32VKIAJUZVHbVn7l7qcNLfywwetMfQbdoJtHrpt8JT0cbZSpe7D4Rn6eNBmTr5DVIW+V0M4TMhoWwAzAv6Ka/w=="',
			],
			body: '{"cows": "are the best"}',
		);

		$this->assertTrue($this->subject->verifyDigest($request));
		$this->assertTrue($this->subject->verifySignature($request, $this->publicKeyPem));
		$this->assertFalse($this->subject->verify($request, $this->publicKeyPem));
	}

	public function testItVerifiesASignedRequestWithinTwentyfourHours() {
		$request = (new MessageSigner())->sign(
			request: new HttpRequest(
				verb: HttpVerb::POST,
				url: 'https://myhost.example/path/to/resource',
				body: '{"cows": "are the best"}',
			),
			keyId: 'testKey',
			keyPem: $this->privateKeyPem,
		);

		$this->assertTrue($this->subject->verify($request, $this->publicKeyPem));
	}
}
