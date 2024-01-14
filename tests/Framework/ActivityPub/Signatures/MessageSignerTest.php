<?php

namespace Smolblog\Framework\ActivityPub\Signatures;

use Psr\Http\Message\RequestInterface;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\TestCase;

final class MessageSignerTest extends TestCase {
	private RequestInterface $getRequest;
	private RequestInterface $postRequest;
	private Follow $postRequestBody;
	private string $privateKeyPem;

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

		$this->privateKeyPem = <<<EOF
		-----BEGIN PRIVATE KEY-----
		MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDHo1zjVKkcP9I+
		jITZX2FVXNSoue5hVJvr5S53bxX/PzznJJO9WlotM02ZiT3fF1HfZPrHVojma7Lk
		ccXxfXsTYCuH2nvwwXRVpme89ZBugyUwYFFjl3Gy7aHOdQtLL4geaID5OTy+zrhS
		mDXNSxd8a4ug2uDDv/l2ueMiAcIXzGcS4/afJw03rIycGJrDAlfbwI3ar38l4MCN
		WdyDDiD+PiYgQDhk59OTFq73ReO7RaNbPAhqHyNURHv3aU8jWEcpz+ET4xGJ1E5m
		4bYZSCw1IlPzWT0p7Guqu6Jay/UBphlJaKmMVT3keRSj7wTSM7eWtFTtCYwzCRqI
		tPcqdYrjAgMBAAECggEAFdHiJiqrrR3AofuDzapiHg1eZO6lglfYDulmscEfe11z
		D6RszWZhss0Hrz9T1t1aonsL+duYbO7ah6Nzyhg36n85YsjbgQ3z5CSi9AE2/w0w
		dGAipSr5T2AvrjwWtuoEC6bKafL6k7ROayCdyMlrULsEcNlbdam232Yj0CS3DRil
		nr+VupAtAXNJQPNNTbdSpUcmSvQP2FfEazkwnAZWuvH8zQ6eqXpRAs4hLjzw7F91
		N6kiRbe2EYWxheQNSMTi6Hq32RxFlFW9LD2DO4FmmKPpZZksMmsMbR61gVrMn6fb
		2uGXGkaW4xcsqSbm7HcC43FntAnkldIytvb36BHqYQKBgQD0L0aUdOyG52DM3Euw
		gma33lfvbFGLhiLISTJN/63rJT+4qeTdUQhRarcy4KT9/G8UE+V3UKWWD0uTWOqF
		HgeA5JmSj7UCP2XWo/iCojb6A6b6YX4iBYiBgpFceZUMjwUgUPr5tgU/D3zbmfyr
		Z4gwhEZ2DTyNVPbLwCehxn7OswKBgQDRTEmhIdVTnPIydrGjDyh++yoXMLRE4Wir
		Nnr6XdDVV3MEmhS+wDVWKIvx3ygqI5vtICfJW7xkTc7y/5QDESOcldpo05aSgOL+
		yO0mtJ+Krp9Wg4EM7rypJChSGavrQUF1h+fO72Bt/gwiudXBVUzgp2pc6Rt+Ca5c
		m9FiuEdrEQKBgGncRQD/X7tse+7UYov3PIjh/8VwdDnEwTeLZB+khMW4tFNedDXu
		d2i0lw+bjGwAEDfoGEcN03umzeDnX2SujBo5AMslOhfrXD8dfxNDOApTowRRV9lw
		BKoA7PvmSdPT/SjxcpznaIbaNAsQSxYUIFrIPbPYMTQkbYoPmB7uavM5AoGAEyye
		AjkLRiG2vpDJLVsSJq/z5zP7D+RmpmjTU2SM4T6ltuI2zFLnkAEe8QW0tEeW3V54
		xqW02KuYLgLkGHPVg17nJ3ta7AkKwrS9pTIe+6GLz200wW6NsiEx4HOhoGfWC2Js
		BjU/7FO94OCNiKy74kj0IZbpgd55LtrHj/e580ECgYEAikgRz3kLhQJ0IjBs1mL+
		Ocs6e8raigsiONEN0Kqz66gR63dHC1aY/RGjjBelmfLU0j/ocirekU5U64uZSFvW
		FncWb8OAtqBvxxsmuPeNhJbByqkuX60eqpwG1WdROV9IE1NjPuRc/RgOyNwHwOrU
		/XTNPCTswV4QAAsfIrRWJbM=
		-----END PRIVATE KEY-----
		EOF;

		$this->subject = new MessageSigner();
	}

	public function testItSignsAPostRequest() {
		$request = $this->postRequest->
			withAddedHeader('Date', 'Tue, 14 Jan 2014 18:50:38 GMT')->
			withAddedHeader('Digest', 'SHA256=un7b964+PCST/h56Qvz9ejegIi6idGxvmwvrFP4DKcY=');
		$keyId = 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor';
		$expected = 'keyId="https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor",algorithm="rsa-sha256",headers="(request-target) host date digest content-type",signature="GJif5eYBq3eaQ+AOh5IRc5xzG0RoDwBGqQXJzpJkflvWblE9GHqx55Wb9WhmKUmuNLAkshoZ5DvF21zb0xo1iu/i/WJGRDpUo2TMxBaoyKR16WD+0uWkR+AX2+QSEK2SjZ8WwK7aHULPxWJMRnZ3E5TUREOhc5BZHid2UdhFUS5p/9KdmCcz503QNbKrqiUuXUxcZ75alWjJtKTn5x4E2JPGGUJ/5oyKJAsyrQeS3cabDy2wpWXI6//wTZPDMgrVab8Vvkhi34ErxKPXp96SZp9O4i1RzPTCNcbOIFaUyquxkv0rhrWTO/4II/jIlT5tLVg9rL7iqDqm08Nu+eWKvw=="';

		echo (new class() { use SignatureKit { generateSignatureSource as public; }})->generateSignatureSource(
			$request,
			['(request-target)', 'host', 'date', 'digest', 'content-type'],
		);

		$this->assertEquals($expected, $this->subject->sign($request, $keyId, $this->privateKeyPem)->getHeaderLine('signature'));
	}
}
