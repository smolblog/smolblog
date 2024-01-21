<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Framework\ActivityPub\Objects\Accept;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubObject;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\ActivityPub\Signatures\MessageSigner;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpResponse;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Test\Kits\HttpMessageComparisonTestKit;
use Smolblog\Test\TestCase;

final class MessageSenderTest extends TestCase {
	use HttpMessageComparisonTestKit;

	private ClientInterface $httpClient;
	private MessageSigner $signer;
	private LoggerInterface $logger;

	protected function setUp(): void
	{
		$this->httpClient = $this->createMock(ClientInterface::class);
		$this->signer = $this->createMock(MessageSigner::class);
		$this->logger = $this->createMock(LoggerInterface::class);

		$this->subject = new MessageSender(
			fetcher: $this->httpClient,
			signer: $this->signer,
			log: $this->logger,
		);
	}

	public function testItCanBeInstantiatedWithOnlyAClient() {
		$basic = new MessageSender(
			fetcher: $this->createStub(ClientInterface::class),
		);

		$this->assertInstanceOf(MessageSender::class, $basic);
	}

	public function testKeyIdIsRequiredIfPemGiven() {
		$this->expectException(ActivityPubException::class);

		$this->subject->send(
			message: new ActivityPubObject(id: $this->randomId()->toString()),
			toInbox: '//test.inbox/',
			signedWithPrivateKey: 'TEST_KEY',
		);
	}

	public function testKeyPemIsRequiredIfKeyGiven() {
		$this->expectException(ActivityPubException::class);

		$this->subject->send(
			message: new ActivityPubObject(id: $this->randomId()->toString()),
			toInbox: '//test.inbox/',
			withKeyId: 'TEST_KEY',
		);
	}

	public function testItWillSendTheMessageAndLogTheAttempt() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$object = new Accept(
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: $actorId,
			object: new Follow(
				id: '//smol.blog/outbox/' . $this->randomId(),
				actor: '//smol.blog/' . $this->randomId() . '/actor.json',
				object: $actorId,
			),
		);

		$inboxUrl = '//smol.blog/' . $this->randomId() . '/inbox';

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::POST,
			url: $inboxUrl,
			body: $object->toArray(),
		);
		$expectedSignedRequest = $expectedRequest->withAddedHeader('Signature', 'Built by oddEvan in South Carolina.');

		$this->signer->expects($this->once())->method('sign')->with(
			request: $this->httpMessageEqualTo($expectedRequest),
			keyId: "$actorId#publicKey",
			keyPem: 'PRIVATE_KEY',
		)->willReturn($expectedSignedRequest);

		$this->logger->expects($this->once())->method('debug')->with(
			$this->equalTo('Sending Accept to ' . $inboxUrl)
		);

		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedSignedRequest)
		)->willReturn(new HttpResponse(code: 204));

		$this->subject->send(
			message: $object,
			toInbox: $inboxUrl,
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#publicKey",
		);
	}

	public function testItLogsRemoteServerErrorsByDefaultAndGivesTheSigningKeyId() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$object = new Accept(
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: $actorId,
			object: new Follow(
				id: '//smol.blog/outbox/' . $this->randomId(),
				actor: '//smol.blog/' . $this->randomId() . '/actor.json',
				object: $actorId,
			),
		);

		$this->signer->method('sign')->willReturnCallback(fn($req) => $req->withAddedHeader('Signature', 'Me!'));
		$this->httpClient->method('sendRequest')->willReturn(
			new HttpResponse(code: 404, body: ['error' => 'inbox does not exist'])
		);

		$this->logger->expects($this->once())->method('error')->with(
			'Error from federated server: {"error":"inbox does not exist"}',
			[
				'message' => $object->toArray(),
				'inbox' => 'https://smol.blog/inbox',
				'signed' => "With key $actorId#publicKey",
			]
		);

		$this->subject->send(
			message: $object,
			toInbox: 'https://smol.blog/inbox',
			signedWithPrivateKey: 'B00',
			withKeyId: "$actorId#publicKey"
		);
	}

	public function testItLogsRemoteServerErrorsAndNotesTheLackOfSignature() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$object = new Accept(
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: $actorId,
			object: new Follow(
				id: '//smol.blog/outbox/' . $this->randomId(),
				actor: '//smol.blog/' . $this->randomId() . '/actor.json',
				object: $actorId,
			),
		);

		$this->httpClient->method('sendRequest')->willReturn(
			new HttpResponse(code: 404, body: ['error' => 'inbox does not exist'])
		);

		$this->logger->expects($this->once())->method('error')->with(
			'Error from federated server: {"error":"inbox does not exist"}',
			[
				'message' => $object->toArray(),
				'inbox' => 'https://smol.blog/inbox',
				'signed' => 'NO',
			]
		);

		$this->subject->send(
			message: $object,
			toInbox: 'https://smol.blog/inbox',
		);
	}

	public function testItCanBeConfiguredToThrowExceptionsOnRemoteServerErrors() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$object = new Accept(
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: $actorId,
			object: new Follow(
				id: '//smol.blog/outbox/' . $this->randomId(),
				actor: '//smol.blog/' . $this->randomId() . '/actor.json',
				object: $actorId,
			),
		);

		$this->signer->method('sign')->willReturnArgument(0);
		$this->httpClient->method('sendRequest')->willReturn(
			new HttpResponse(code: 404, body: ['error' => 'inbox does not exist'])
		);

		$this->expectException(ActivityPubException::class);

		(new MessageSender(
			fetcher: $this->httpClient,
			signer: $this->signer,
			log: $this->logger,
			throwOnError: true,
		))->send(
			message: $object,
			toInbox: 'https://smol.blog/inbox',
			signedWithPrivateKey: 'B00',
			withKeyId: "$actorId#publicKey"
		);
	}

	public function testItWillSkipSigningIfNoSignerIsConfigured() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$object = new Accept(
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: $actorId,
			object: new Follow(
				id: '//smol.blog/outbox/' . $this->randomId(),
				actor: '//smol.blog/' . $this->randomId() . '/actor.json',
				object: $actorId,
			),
		);

		$inboxUrl = '//smol.blog/' . $this->randomId() . '/inbox';
		$expectedRequest = new HttpRequest(
			verb: HttpVerb::POST,
			url: $inboxUrl,
			body: $object->toArray(),
		);

		$this->logger->expects($this->once())->method('debug')->with(
			$this->equalTo('Sending Accept to ' . $inboxUrl)
		);
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedRequest)
		)->willReturn(new HttpResponse(code: 204));

		(new MessageSender(fetcher: $this->httpClient, log: $this->logger))->send(
			message: $object,
			toInbox: $inboxUrl,
			signedWithPrivateKey: 'PRIVATE_KEY',
			withKeyId: "$actorId#publicKey",
		);
	}

	public function testItWillSkipSigningIfNoKeyIsGiven() {
		$actorId = '//smol.blog/' . $this->randomId() . '/actor.json';
		$object = new Accept(
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: $actorId,
			object: new Follow(
				id: '//smol.blog/outbox/' . $this->randomId(),
				actor: '//smol.blog/' . $this->randomId() . '/actor.json',
				object: $actorId,
			),
		);

		$inboxUrl = '//smol.blog/' . $this->randomId() . '/inbox';

		$expectedRequest = new HttpRequest(
			verb: HttpVerb::POST,
			url: $inboxUrl,
			body: $object->toArray(),
		);

		$this->signer->expects($this->never())->method('sign');

		$this->logger->expects($this->once())->method('debug')->with(
			$this->equalTo('Sending Accept to ' . $inboxUrl)
		);

		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo($expectedRequest)
		)->willReturn(new HttpResponse(code: 204));

		$this->subject->send(
			message: $object,
			toInbox: $inboxUrl,
		);
	}
}
