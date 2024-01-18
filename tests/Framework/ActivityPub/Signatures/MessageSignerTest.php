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
final class MessageSignerTest extends TestCase {
	private string $privateKeyPem;

	protected function setUp(): void {
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

		$this->subject = new MessageSigner();
	}

	public function testItSignsTheGetRequestFromTheFediSpec() {
		$request = new HttpRequest(
			verb: HttpVerb::GET,
			url: 'https://myhost.example/path/to/resource',
			headers: ['Date' => 'Wed, 15 Mar 2023 17:28:15 GMT'],
		);
		$keyId = 'https://remote.example/actor#key';
		$expected = 'keyId="https://remote.example/actor#key",algorithm="rsa-sha256",headers="(request-target) host date",signature="hUW2jMUkhiKTmAoqgq7CDz0l4nYiulbVNZflKu0Rxs34FyBs0zkBKLZLUnR35ptOvsZA7hyFOZbmK9VTw2VnoCvUYDPUb5VyO3MRpLv0pfXNExQEWuBMEcdvXTo30A0WIDSL95u7a6sQREjKKHD5+edW85WhhkqhPMtGpHe95cMItIBv6K5gACrsOYf8TyhtYqBxz8Et0iwoHnMzMCAHN4C+0nsGjqIfxlSqUSMrptjjov3EBEnVii9SEaWCH8AUE9kfh3FeZkT+v9eIDZdhj4+opnJlb9q2+7m/7YH0lxaXmqro0fhRFTd832wY/81LULix/pWTOmuJthpUF9w6jw=="';

		$signed = $this->subject->sign($request, $keyId, $this->privateKeyPem);

		$this->assertEquals($expected, $signed->getHeaderLine('signature'));
	}

	public function testItDigestsAndSignsThePostRequestFromTheFediSpec() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://myhost.example/path/to/resource',
			headers: ['Date' => 'Wed, 15 Mar 2023 17:28:15 GMT'],
			body: '{"cows": "are the best"}',
		);
		$keyId = 'https://remote.example/actor#key';

		$expectedHash = 'sha-256=VOV9b4OFUAdF0mGBVK62bE+PT3t0UtTEfq7hNT3zv9U=';
		$expected = 'keyId="https://remote.example/actor#key",algorithm="rsa-sha256",headers="(request-target) host date digest",signature="gat6knmRUKkFUT2Pz66fjPXfhmUPx8peccozPFeGDrOixfjgmmyvaVgknnINlC7k9xE67//rVy5On7esftVuSzL4z39tbFd9WsPvQ+nDuFynD1q8vPRt4BLNDr4WbxG+jLPQJBPoHReaZqPe/nPSzpfTU9qNKpLWx78yoYkW1ag71on74M8K/X7x6DNq0TBJQqxsADsfyiOeDftPv3AonBZOQBYP9fucBKmCurRNXyn3jdaYGW+cDlMQECBI78yd32VKIAJUZVHbVn7l7qcNLfywwetMfQbdoJtHrpt8JT0cbZSpe7D4Rn6eNBmTr5DVIW+V0M4TMhoWwAzAv6Ka/w=="';

		$signed = $this->subject->sign($request, $keyId, $this->privateKeyPem);

		$this->assertEquals($expectedHash, $signed->getHeaderLine('digest'));
		$this->assertEquals($expected, $signed->getHeaderLine('signature'));
	}

	public function testItSignsTheContentTypeHeaderIfItExists() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/api/preview/markdown',
			headers: ['Content-type' => 'text/markdown'],
			body: 'And you may ask yourself: _Well, how did I get here?_',
		);

		$signed = $this->subject->sign($request, 'key', $this->privateKeyPem);

		$this->assertStringContainsString('content-type', $signed->getHeaderLine('signature'));
	}
}
