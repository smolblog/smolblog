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
		MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDE2F8xQWsvzXKC
		gEpv8t414/eD3RzqbzY4pGmc4rLtqnkO7ztiJbFSaVoiIWZDQt4kYcoCE9OUTuUz
		Rv9wm5Dh6bzR2erDURqLbjdewh6bdrG9vtfwZ0mCAwXZbcoV9GxLA5FOJlMFWcu7
		awl6IAQmQLJ4sgd5ryZz6Z0wS8ybLtaWxlPxIuFnB1i5JUeh80oEb6J0pUstI8kf
		bemzkRmlTedNJTtTmcewb2X36Nmu8g7VjlnXzBA+e5siiCsZO4Vxn7qtJOhis+7F
		WIKx54BP8uVnF75PQc2A9nRUpdb+VPmVlCNUbMmDzuQzoZs/f3BDCSFBNGA/EsIA
		SCGLDTYpAgMBAAECgf807U4AcGvhmTFbkCMeTARzeQLpnF+p/pJq/coSj4yMsB7c
		unZnwrrAQz4jmddlPmrRss7IwDajfz3MC8r4UYZ4jTW34bVoeZDUQfj2rDs5PIY4
		qUJmjAm3wML8iLNNiUA/leCmoqdankpT755yDSm7H1TddFY44bSfy1tNy75ipZHu
		B5y5m7kjHvM/8h5dMursQdbRfb5Nwy0WxOs4yrSC0kwqxE6Bw5QlkfKsNcON4rbt
		E5EHYBAIpp7IENsQl0gU8OCiYmUpXdUXHbaJEgcbc+gk9kA8ZqczvGNpIokKTUIN
		F9orcjYWACdqcO/nqijV0ASmjssptMtNinlEgNcCgYEA6FFUAnRO5b4CBWDp8XcZ
		SH5I+xNqxnJ6eXVSOBL21awRfRkuxVe0t4ggKqMN5V8s3lfZVRSTqrF8V9B5XLx0
		HUXFQ1YmZOlUgzowP2DDfcigwzmNpDNnXfnHWcBW7HjAPIHsxJcmgjeeQvWXJpyn
		nMzGV8E1JeZ3AJkfiTVnKo8CgYEA2OlWRMNKkV831CKw+oQrZhuBavkarSmYB7G/
		7Sqqp0OFB7BQcAHUoOpj9GKGRSZNzCe0bSBqQ8wMU7A0G1u80iTTzJmVpgPd5JNo
		ODdK02D2V750LTVSeuwDYLKYQoJ3vGJPFxkkoQ6n/Uq0ORUjNWY/nQHiTVxUAxLa
		FaM3T8cCgYEAq8PN0E68MsLMbbuc3IoJKH8uaGyPyo/Dm9+xXYIv/AVPnOWsnf3d
		wMClv8B0ur0myHG9X1lqYI7/d1HNGVLAqw/17HZaxst5T9kK6SEbCPn4Wr9HyTq0
		V1ghG2vJGOeigloxe5yhvS9wFOPN0J+MkbMZhrJ0IESIgem01lako2MCgYEAgkgs
		A6MUr5n0S1sG14D5HBwbMGgsSKN55+0wLL/6vfoE9ehFd/DxiDEgFhHzOadXBO84
		JG9axBvTr5Rex6vjDokGGZaJ1qvt47NR3qn/LFTJwUvoNZsYTjJhmPvMDe6VAjY9
		8M6uPHo0FiN/eeWjPxiJzsDp7rMs1qfFC0f8GdMCgYAxA1X15gapZvEmWepdFztn
		XPWh9qv6jq5czy+BBb7X+BX5bay0g4jdMfbGYxtLFlhUW8qXX6y4Ioyc2Np4fcld
		bwYS/pqbGT2SsI0Fwzs0slU0ApX+/6KkasTWBjRqDL4gcW7lfoM4YxUNVFevBoRP
		movcb8fKpA+Hd+5LOefq9A==
		-----END PRIVATE KEY-----
		EOF;

		$this->subject = new MessageSigner();
	}

	public function testItSignsAPostRequest() {
		$request = $this->postRequest->
			withAddedHeader('Date', 'Tue, 14 Jan 2014 18:50:38 GMT')->
			withAddedHeader('Digest', 'SHA256=un7b964+PCST/h56Qvz9ejegIi6idGxvmwvrFP4DKcY=');
		$keyId = 'https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor';
		$expected = 'keyId="https://smol.blog/site/c88e0395-cece-4037-8a2c-7be481a3c1fe/activitypub/actor",algorithm="rsa-sha256",headers="(request-target) host date digest content-type",signature="cRRgm7qEsbUKjvP3SYHSHhSQD/s4H7eGg0vyfO1+LC7/5GkhzRwMI230EbY5Xjgr8SWNrZ9WI816eucjv5f9OMRgdfml3ZflxlxahJc5GxGlXUiKHeH2m0O64W0b6FS2RM/VLyqmLu96kzE4c//MecmqwY2ANrPaw8kwa1Ay9jeJtXjdo/jTK2lJJ08gIFDFmi77hHjPsiij5sSPsV/z0bBb406qNTi5WAAFEr0MVtSSux605h3ROF4Oh0U3fkeOii/4CtX7oi5Y7gSdKOZLoyUsrQBzulE0fFJr4ead8zK7x/tUxxK7rmSlZqol27KjebVq8x7q7x68emdh4fx6ug=="';

		echo (new class() { use SignatureKit { generateSignatureSource as public; }})->generateSignatureSource(
			request: $request,
			headers: ['(request-target)', 'host', 'date', 'digest', 'content-type'],
		);

		$this->assertEquals($expected, $this->subject->sign($request, $keyId, $this->privateKeyPem)->getHeaderLine('signature'));
	}
}
