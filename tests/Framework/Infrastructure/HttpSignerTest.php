<?php

namespace Smolblog\Framework\Infrastructure;

use DateTimeInterface;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\TestCase;

final class HttpSignerTest extends TestCase {
	const PRIVATE_KEY = <<<EOF
	-----BEGIN RSA PRIVATE KEY-----
	MIIEpAIBAAKCAQEAsp+ROePbM/54gm2YirSGQTAmaKzmYwDgfR5NaITJbizQS9F3
	CnJsGKQWprAW8bbnXQVyBDb9yP9OM+0/OtqHc2NxW4ZNovSO/ehJqZ34yDFK9pg9
	UMksP7GnlKp2WIHWJMg+YBo8wc4Bpelm7PTarcqi8QOb2LxMUgaI6Fm/g3/NsSWv
	aJ3gsZLu9DzheYlvLC5ggY+H91VNyBzsA7EbaENmFuAMBp5egMFstZa8d4Cq4huQ
	bUvuy2LfeNiPCTGZHA+AqW//w2T+FslEKiJf1vlkqAXEdC+xmVI6FoSKIfLa/wNb
	eI2Hz+u7dTJN1U+OJg+Cxhzra8soRVWaQvl3LQIDAQABAoIBAFsFnQeW6PjRz68H
	Ehh8bX2Cf9APa0dAByU67882+z49b9dy4epc7GCSHUjLqqV5wuRQBg3HyANIth0X
	5ISlHyjZn8Y35hAH92XDnOXmLgQ1ujF22qf0G6xJZ1AGnU/0uZ78u2xVcmiABa76
	BQzyqQyumeGfKSeErI+P7OwZ79Gd0KwJfzQLexNQd1qbFAMVXUKeMmH34vS2B1fN
	05AXxsDTxWmn+IbH3Vc5vfoiEDkjyBGZLcxiZE5gHPr69l15pue3cZECoPZ3Cn4D
	rbnzYEBeNArGt1vbN+zeXgg5OnavMVyR2sEiHW/Lc3bnjOFdybNwiz51mgqgAZUO
	4q9QcrECgYEA3Y+8NEGnTgWc/8Mob1YSv+agFctC6GUqjPaV8qYvDd6sQH7ZbQ3d
	cKMMqxfXjgz+7/Q3xCVHA5a+D6Sp6SG/DtWB6egy/oFtg3FybIMUu4nvDWVIuchy
	In8xNEdyRRuRPR+FV5l5FsKsxslSY9FZIrIW7vjdBpAfv1ycaB3f/K8CgYEAzmNC
	FfRhYGzkSHT6dGvwWamcexiV3lzmIWQNvW+yI+qWNbYYU7xgX1q5OUrV4lF0oMta
	vetbKqu7sN3nEmJXjen3JkgmssDwguqmcaBcRhuvk8AA8UNBCnS/1NoVtsCUwtqH
	7Js/gKTtAD5jDwSWBkIBO/Y1QBOsiu+mR3gUGOMCgYEAgedZaKYpyuQdphOtrIGh
	4qP8rmqLoyhVp2qYhjmLky1Af1wgbQFZGUZwEgyblKzn+JaO79EPbvo+G3vnJ0pi
	8/aZAiTjaTdHl263sQm16TM5VvhQiKUOzk0W81kElaJRKK5HhxHz3jVsCe1WAjJn
	eaFDMv/0z3lHM/K/vYfuoP8CgYBZvS7u/OOaWb6pArQkCwrm8ajonTgNB7fIrQiM
	ZhS/KTFHCXZqcm41B+2hy7hUP7bGc6VxDvUFCMcDkHj4tWn8es7MBnNNJjdttTnK
	DkAQ+9jMFaBTRzrwoPMISgtG+1Wzo/GWH6rs9MlYYcgQr53L+scum09sHSHZB3r1
	eHDEoQKBgQCeoyVIkhaezeCQsvrwWT4wurqs/+yz3d2FeX+SDF7sLa9Kp8RLY0TJ
	uhuMkThJ1ZW9QD8zFe+KcdYDgWc7n0kLAHJkHpOAl9fJgIFnSjoDicurDxRicV5p
	MnQVV7oVQ9/tNIUBMJQZsI2FOJMHlbxcsYuY0eobeORy4TnKM1u3KQ==
	-----END RSA PRIVATE KEY-----
	EOF;

	public function testItAddsAValidSignatureToAHttpRequest() {
		$request = (new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/activitypub/inbox',
			body: ['thing' => 'happened']
		))->withAddedHeader('Date', 'Sat, 30 Apr 2016 17:52:13 GMT');


		$service = new HttpSigner();
		$request = $service->sign(request: $request, key: self::PRIVATE_KEY);

		echo $request->getHeaderLine('Signature');

		$this->assertEquals(
			'keyId="key",algorithm="hmac-sha256",headers="(request-target) date host",signature="BjQJQz9RBGnUPYGaYyuwkw2NZNmaX3mbIUEcHq96LPI="',
			$request->getHeaderLine('Signature')
		);
	}

	public function testItAddsADateHeaderIfNoneExists() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/activitypub/inbox',
			body: ['thing' => 'happened']
		);

		$service = new HttpSigner();
		$request = $service->sign(request: $request, key: self::PRIVATE_KEY);

		$this->assertEquals(date(DateTimeInterface::RFC7231), $request->getHeaderLine('Date'));
	}
}
