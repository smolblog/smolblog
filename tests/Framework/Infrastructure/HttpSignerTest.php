<?php

namespace Smolblog\Framework\Infrastructure;

use DateTimeInterface;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Framework\Objects\Keypair;
use Smolblog\Test\TestCase;

final class HttpSignerTest extends TestCase {
	private Keypair $keypair;
	public function setUp(): void {
		$this->keypair = Keypair::jsonDeserialize('{"privateKey": "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCfQilRaBBSsYft\nXohIBL6QVoCB4zGaIGxSo7bfGTKnC3oZw/PkSSE3g3J8uG/b28fdAvSZKUv5sCrq\nnxPFw1s+IuKeMzunLo39FnkRcpP08jZno7A1oe49GI6HTK2XINXszymfSuEuKpcw\nlpBUAu8Vbr6dtYgsjIIjvBx48uXaAO61dGSVR0Aaxq7BW1yDIaXByXbE+iXG/Sx2\nntg0ol4VEy+uk5ncbFTkKd6BnJ42FPBilESbRhUxI8n2+iDCnXkZtkFs6C8E2WNQ\nkDvpzLutCpRl2FgIrH4RagXqLqfxajQC9rmlI9n9IkYljqgETKPoG2opXnwmMhsQ\nIkv5X3FhAgMBAAECggEAQlLOXD2nVpjij8MnpD59kiTEHdOdC5/nHL9bYCvhQVnx\nPpuxjWe7MqBGZJR22Sv9Xxhk/wgIwPJR3SXkmR6TeBwVHmcdt7EWpsjeIJsD7SWV\n7LFpp9xJGB6K9OPFA0REcvuPTOxlPNG15yE8+A/Eu7tEHO/Fxy+43uLvRJt7h77l\nesDo3dtF1HODmf/al4iCX9AJPHeyE8vbWantW1HWknP9fJ5nMyELR2kU7cIHu+Xc\nGsRJK/SBzh6AlTbgvhqb8HXyvLJ7pPt5tb+5unNc4AR6YJZp9lA49wnQPrjaoDtI\nsXkxGG1mOIoU8MusKVx3OqvYyRdj6rh/xQZ79ow4dQKBgQDL3EoLYIwiKcHoYbT/\nv4euuAOVLXw3oJXn1KMLqtHIrDMOCdTFKqx39TWHDIyt8X754MStAq90sSsR5ry1\nLen9CJfQ0vA0EKvUiGkcW7/aFn8/8I4X9IpJSuO1oUibSR6xCSXZNjfWRzK+ScWm\no9l9DGawMDHys6DprUJ7WTHA5wKBgQDH/ZB6ZyU95Cy0Ka8nq96udubg5v6uI2vY\nETlix6wKlmPQQnTm25lwoZZdAI61utuxCQGTv4RivPSYQAs9QTWLBt4yS6MEDDnz\ngh60h+pGxcu8kBIuxpI5qOynHfBOvI5S6qQCHo1UbGyV7eHaQ1uE0DcSYKYsNlrh\nQCX01yRKdwKBgAYPOjQ0XnX1f8oEfXjMnJ/Y4GJiw7pzj4EglOgX37xzQeE88ZIa\nvp2iMEEfYl8ZOoj64V2zIrv5OCqEDT/laXsX8ktGudUSWckrdNRe9cjpukaQQ+j6\nX9Hl4/bWIG5dMghZGULnlalM3HlDgBh/7ksFP1glVpa8OCA6AivgbtYpAoGBAI74\nC0gl4q7LJtYpEolWyduJLuZK3Hia4+bT8WVXfvsWpgZk6/N5u8iUC80yr9Lk4Vc/\nK/x2pmp70JPi/OXubxuTblcgUUp8fxVAyTigDXBIyKxlhkogNLq5s2yI75kqHMjT\n6ymEs95NoJbSN2p0SsG4pBYkN8dVmER9OmU9RDljAoGBALbSg8f/Y1YGAM4ilpw1\nO4t1M9AfaP9ezqC67CwMI2E0k36AP8cM7OqEZG1BBup3K3U6L9yDnH0ND6LZXUnZ\n8ySPr1jpOYo0NYAwwLHUyL2u5GxYTGPiSuEPOHQ3QT94kOaugho/lmnLia8pbcVd\npCoZvHStMlNiu7hZoYFkD946\n-----END PRIVATE KEY-----","publicKey": "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAn0IpUWgQUrGH7V6ISAS+\nkFaAgeMxmiBsUqO23xkypwt6GcPz5EkhN4NyfLhv29vH3QL0mSlL+bAq6p8TxcNb\nPiLinjM7py6N/RZ5EXKT9PI2Z6OwNaHuPRiOh0ytlyDV7M8pn0rhLiqXMJaQVALv\nFW6+nbWILIyCI7wcePLl2gDutXRklUdAGsauwVtcgyGlwcl2xPolxv0sdp7YNKJe\nFRMvrpOZ3GxU5CnegZyeNhTwYpREm0YVMSPJ9vogwp15GbZBbOgvBNljUJA76cy7\nrQqUZdhYCKx+EWoF6i6n8Wo0Ava5pSPZ/SJGJY6oBEyj6BtqKV58JjIbECJL+V9x\nYQIDAQAB\n-----END PUBLIC KEY-----"}');
	}

	public function testItAddsAValidSignatureToAHttpRequest() {
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

	public function testItAddsADateHeaderIfNoneExists() {
		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: 'https://smol.blog/activitypub/inbox',
			body: ['thing' => 'happened']
		);

		$service = new HttpSigner();
		$dateString = date(DateTimeInterface::RFC7231);
		$request = $service->sign(
			request: $request,
			keyId: 'https://smolblog.localhost/api/site/0c2f2fe8-8098-4868-a6f7-7a37dc679662/activitypub/actor#publicKey',
			keyPem: $this->keypair->privateKey
		);

		// TODO: This test can fail if the previous line takes place in the previous second.
		$this->assertEquals($dateString, $request->getHeaderLine('Date'));
	}
}
