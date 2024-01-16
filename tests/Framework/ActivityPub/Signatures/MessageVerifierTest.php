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

		$this->assertTrue($this->subject->verifySignature($request, $this->publicKeyPem));
	}

	public function xtestItVerifiesARealRequest() {
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
