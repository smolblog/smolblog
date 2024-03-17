<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubObject;
use Smolblog\Framework\ActivityPub\Objects\Actor;
use Smolblog\Framework\ActivityPub\Objects\ActorType;
use Smolblog\Framework\ActivityPub\Signatures\MessageSigner;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpResponse;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\Kits\HttpMessageComparisonTestKit;
use Smolblog\Test\TestCase;

final class ObjectGetterTest extends TestCase {
	use HttpMessageComparisonTestKit;

	private ClientInterface $httpClient;
	private MessageSigner $signer;

	protected function setUp(): void
	{
		$this->httpClient = $this->createMock(ClientInterface::class);
		$this->signer = $this->createMock(MessageSigner::class);

		$this->subject = new ObjectGetter(
			fetcher: $this->httpClient,
			signer: $this->signer,
			log: new NullLogger(),
			throwOnError: true,
		);
	}

	public function testItCanBeInstantiatedWithOnlyAClient() {
		$basic = new ObjectGetter(
			fetcher: $this->createStub(ClientInterface::class),
		);

		$this->assertInstanceOf(ObjectGetter::class, $basic);
	}

	public function testKeyIdIsRequiredIfPemGiven() {
		$this->expectException(ActivityPubException::class);

		$this->subject->get(
			url: '//test.inbox/',
			signedWithPrivateKey: 'TEST_KEY',
		);
	}

	public function testKeyPemIsRequiredIfKeyGiven() {
		$this->expectException(ActivityPubException::class);

		$this->subject->get(
			url: '//test.inbox/',
			withKeyId: 'TEST_KEY',
		);
	}

	public function testItWillRetrieveTheObject() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$actor = new Actor(
			id: $actorId,
			type: ActorType::Application,
			inbox: '//smol.blog/inbox'
		);

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::GET,
			url: $actorId,
			headers: ['accept' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'],
		);
		$expectedSignedRequest = $expectedRequest->withAddedHeader('signature', 'Built by oddEvan in South Carolina.');

		$this->signer->expects($this->once())->method('sign')->with(
			request: $this->httpMessageEqualTo($expectedRequest),
			keyId: "$actorId#publicKey",
			keyPem: 'PRIVATE_KEY',
		)->willReturn($expectedSignedRequest);

		$response = new HttpResponse(body: $actor);
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedSignedRequest)
		)->willReturn($response);

		$this->assertEquals($actor, $this->subject->get(
			url: $actorId,
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#publicKey",
		));
	}

	public function testItWillSkipSigningIfNoSignerIsConfigured() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$actor = new Actor(
			id: $actorId,
			type: ActorType::Application,
			inbox: '//smol.blog/inbox'
		);

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::GET,
			url: $actorId,
			headers: ['accept' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'],
		);

		$response = new HttpResponse(body: $actor);
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedRequest)
		)->willReturn($response);

		$this->assertEquals($actor, (new ObjectGetter(fetcher: $this->httpClient))->get(
			url: $actorId,
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#publicKey",
		));
	}

	public function testItWillSkipSigningIfNoKeyIsGiven() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$actor = new Actor(
			id: $actorId,
			type: ActorType::Application,
			inbox: '//smol.blog/inbox'
		);

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::GET,
			url: $actorId,
			headers: ['accept' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'],
		);

		$response = new HttpResponse(body: $actor);
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedRequest)
		)->willReturn($response);

		$this->assertEquals($actor, $this->subject->get($actorId));
	}

	/** @see https://arcanican.is/excerpts/cve-2024-23832/ */
	function testItWillBailIfTheObjectDoesNotMatchTheUrl() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$actor = new Actor(
			id: '//mastodon.social/oddevan',
			type: ActorType::Application,
			inbox: '//smol.blog/inbox'
		);

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::GET,
			url: $actorId,
			headers: ['accept' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'],
		);
		$expectedSignedRequest = $expectedRequest->withAddedHeader('signature', 'Built by oddEvan in South Carolina.');

		$this->signer->expects($this->once())->method('sign')->with(
			request: $this->httpMessageEqualTo($expectedRequest),
			keyId: "$actorId#publicKey",
			keyPem: 'PRIVATE_KEY',
		)->willReturn($expectedSignedRequest);

		$response = new HttpResponse(body: $actor);
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedSignedRequest)
		)->willReturn($response);

		$this->expectException(ActivityPubException::class);
		$this->subject->get(
			url: $actorId,
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#publicKey",
		);

		$this->assertNull((new ObjectGetter(
			fetcher: $this->httpClient,
			signer: $this->signer,
			log: new NullLogger(),
			throwOnError: false,
		))->get(
			url: $actorId,
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#publicKey",
		));
	}

	public function testItWillIgnoreTheFragmentWhenMatchingTheObjectUrl() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$actor = new Actor(
			id: $actorId,
			type: ActorType::Application,
			inbox: '//smol.blog/inbox'
		);

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::GET,
			url: "$actorId#publicKey",
			headers: ['accept' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'],
		);
		$expectedSignedRequest = $expectedRequest->withAddedHeader('signature', 'Built by oddEvan in South Carolina.');

		$this->signer->expects($this->once())->method('sign')->with(
			request: $this->httpMessageEqualTo($expectedRequest),
			keyId: "$actorId#signingKey",
			keyPem: 'PRIVATE_KEY',
		)->willReturn($expectedSignedRequest);

		$response = new HttpResponse(body: $actor);
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedSignedRequest)
		)->willReturn($response);

		$this->assertEquals($actor, $this->subject->get(
			url: "$actorId#publicKey",
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#signingKey",
		));
	}

	public function testItWillReturnNullIfTheObjectDoesNotMatchTheUrl() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$actor = new Actor(
			id: '//mastodon.social/oddevan',
			type: ActorType::Application,
			inbox: '//smol.blog/inbox'
		);

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::GET,
			url: $actorId,
			headers: ['accept' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'],
		);
		$expectedSignedRequest = $expectedRequest->withAddedHeader('signature', 'Built by oddEvan in South Carolina.');

		$this->signer->expects($this->once())->method('sign')->with(
			request: $this->httpMessageEqualTo($expectedRequest),
			keyId: "$actorId#publicKey",
			keyPem: 'PRIVATE_KEY',
		)->willReturn($expectedSignedRequest);

		$response = new HttpResponse(body: $actor);
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedSignedRequest)
		)->willReturn($response);

		$this->assertNull((new ObjectGetter(
			fetcher: $this->httpClient,
			signer: $this->signer,
			log: new NullLogger(),
			throwOnError: false,
		))->get(
			url: $actorId,
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#publicKey",
		));
	}
}
