<?php

namespace Smolblog\Framework\ActivityPub;

use Nyholm\Psr7\ServerRequest;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Framework\ActivityPub\Objects\Activity;
use Smolblog\Framework\ActivityPub\Objects\Actor;
use Smolblog\Framework\ActivityPub\Objects\ActorType;
use Smolblog\Framework\ActivityPub\Objects\Delete;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\ActivityPub\Objects\Undo;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpResponse;
use Smolblog\Framework\Objects\HttpVerb;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\HttpMessageComparisonTestKit;
use Smolblog\Test\TestCase;

final class InboxAdapterTest extends TestCase {
	use HttpMessageComparisonTestKit;

	private ClientInterface $httpClient;
	private MessageVerifier $verifier;
	private LoggerInterface $logger;
	private Identifier $inbox;

	protected function setUp(): void {
		$this->httpClient = $this->createMock(ClientInterface::class);
		$this->verifier = $this->createMock(MessageVerifier::class);
		$this->logger = $this->createMock(LoggerInterface::class);
		$this->inbox = $this->randomId();

		$this->subject = new class(
			fetcher: $this->httpClient,
			verifier: $this->verifier,
			log: $this->logger,
			inbox: $this->inbox,
		) extends InboxAdapter {
			public function __construct(private mixed $inbox, mixed ...$props) { parent::__construct(...$props); }
			protected function determineInbox(ServerRequestInterface $request): mixed { return $this->inbox; }
		};
	}

	public function testItCanBeCreatedWithNothing() {
		$this->assertInstanceOf(
			InboxAdapter::class,
			new class() extends InboxAdapter {}
		);
	}

	public function testItWillAttemptVerificationIfARequestIsSigned() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			['Signature' => 'keyId="'.$message->actor.'#publicKey",algorithm="RSA",digest="SHANANANANAAAAA"'],
			json_encode($message)
		);

		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo(new HttpRequest(
				verb: HttpVerb::GET,
				url: "$message->actor#publicKey",
				headers: ['Accept' => 'application/json']
			))
		)->willReturn(new HttpResponse(body: [
			'@context' => [
				'https://www.w3.org/ns/activitystreams',
				'https://w3id.org/security/v1'
			],
			'type' => 'Person',
			'id' => $message->actor,
			'publicKey' => [
				'id' => "$message->actor#publicKey",
				'owner' => $message->actor,
				'publicKeyPem' => 'PUBLIC_KEY',
			],
		]));

		$this->verifier->expects($this->once())->method('verify')->with(
			request: $this->httpMessageEqualTo($request),
			keyId: "$message->actor#publicKey",
			keyPem: 'PUBLIC_KEY',
		)->willReturn(true);

		$this->subject->handleRequest($request);
	}

	public function testItWillSkipVerificationIfARequestIsNotSigned() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			[],
			json_encode($message)
		);

		$this->verifier->expects($this->never())->method('verify');

		$this->subject->handleRequest($request);
	}

	public function testItWillSkipVerificationIfItDoesNotHaveAClient() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			['Signature' => 'keyId="'.$message->actor.'#publicKey",algorithm="RSA",digest="SHANANANANAAAAA"'],
			json_encode($message)
		);

		$this->verifier->expects($this->never())->method('verify');

		(new class(verifier: $this->verifier) extends InboxAdapter {})->handleRequest($request);
	}

	public function testItWillSkipVerificationIfItDoesNotHaveAVerifier() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			['Signature' => 'keyId="'.$message->actor.'#publicKey",algorithm="RSA",digest="SHANANANANAAAAA"'],
			json_encode($message)
		);

		$this->httpClient->expects($this->never())->method('sendRequest');

		(new class(fetcher: $this->httpClient) extends InboxAdapter {})->handleRequest($request);
	}

	public function testItWillFailVerificationIfTheKeyIdIsNotAUrl() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			['Signature' => 'keyId="'.$this->randomId().'",algorithm="RSA",digest="SHANANANANAAAAA"'],
			json_encode($message)
		);

		$this->httpClient->expects($this->never())->method('sendRequest');

		$this->verifier->expects($this->never())->method('verify');

		$this->logger->expects($this->once())->method('info')->with(
			'Request provided invalid signature',
			['request' => $request]
		);
		$this->logger->expects($this->never())->method('debug');

		$this->subject->handleRequest($request);
	}

	public function testItWillFailVerificationIfTheKeyActorUrlDoesNotHaveAPublicKey() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			['Signature' => 'keyId="'.$message->actor.'#publicKey",algorithm="RSA",digest="SHANANANANAAAAA"'],
			json_encode($message)
		);

		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo(new HttpRequest(
				verb: HttpVerb::GET,
				url: "$message->actor#publicKey",
				headers: ['Accept' => 'application/json']
			))
		)->willReturn(new HttpResponse(body: [
			'@context' => [
				'https://www.w3.org/ns/activitystreams',
				'https://w3id.org/security/v1'
			],
			'type' => 'Person',
			'id' => $message->actor,
		]));

		$this->verifier->expects($this->never())->method('verify');

		$this->logger->expects($this->once())->method('info')->with(
			'Request provided invalid signature',
			['request' => $request]
		);
		$this->logger->expects($this->never())->method('debug');

		$this->subject->handleRequest($request);
	}

	public function testItWillFailVerificationIfTheKeyUrlIsNotAnActor() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			['Signature' => 'keyId="'.$message->actor.'#publicKey",algorithm="RSA",digest="SHANANANANAAAAA"'],
			json_encode($message)
		);

		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo(new HttpRequest(
				verb: HttpVerb::GET,
				url: "$message->actor#publicKey",
				headers: ['Accept' => 'application/json']
			))
		)->willReturn(new HttpResponse(body: ['smol' => 'blog']));

		$this->verifier->expects($this->never())->method('verify');

		$this->logger->expects($this->once())->method('info')->with(
			'Request provided invalid signature',
			['request' => $request]
		);
		$this->logger->expects($this->never())->method('debug');

		$this->subject->handleRequest($request);
	}

	public function testItWillStopIfTheRequestSignatureIsInvalid() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest(
			'POST',
			'https://smol.blog/inbox',
			['Signature' => 'keyId="'.$message->actor.'#publicKey",algorithm="RSA",digest="SHANANANANAAAAA"'],
			json_encode($message)
		);

		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo(new HttpRequest(
				verb: HttpVerb::GET,
				url: "$message->actor#publicKey",
				headers: ['Accept' => 'application/json']
			))
		)->willReturn(new HttpResponse(body: [
			'@context' => [
				'https://www.w3.org/ns/activitystreams',
				'https://w3id.org/security/v1'
			],
			'type' => 'Person',
			'id' => $message->actor,
			'publicKey' => [
				'id' => "$message->actor#publicKey",
				'owner' => $message->actor,
				'publicKeyPem' => 'PUBLIC_KEY',
			],
		]));

		$this->verifier->expects($this->once())->method('verify')->with(
			request: $this->httpMessageEqualTo($request),
			keyId: "$message->actor#publicKey",
			keyPem: 'PUBLIC_KEY',
		)->willReturn(false);

		$this->logger->expects($this->once())->method('info')->with(
			'Request provided invalid signature',
			['request' => $request]
		);
		$this->logger->expects($this->never())->method('debug');

		$this->subject->handleRequest($request);
	}

	public function testItWillDelegateAFollowMessage() {
		$message = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->never())->method('sendRequest');

		$this->logger->expects($this->once())->method('debug')->with(
			'Unhandled Follow request received',
			['inbox' => $this->inbox, 'message' => $message->toArray()]
		);

		$this->subject->handleRequest($request);
	}

	public function testItWillDelegateAnUndoMessageForAnIncludedFollowMessage() {
		$follow = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$message = new Undo(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: $follow,
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->never())->method('sendRequest');

		$this->logger->expects($this->once())->method('debug')->with(
			'Unhandled Undo Follow request received',
			['inbox' => $this->inbox, 'message' => $message->toArray(), 'request' => $follow->toArray()]
		);
		$this->logger->expects($this->never())->method('error');

		$this->subject->handleRequest($request);
	}

	public function testItWillDelegateAnUndoMessageForARemoteFollowMessage() {
		$follow = new Follow(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
		);
		$message = new Undo(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: $follow->id,
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo(new HttpRequest(
				verb: HttpVerb::GET,
				url: $follow->id,
				headers: ['Accept' => 'application/json'],
			))
		)->willReturn(new HttpResponse(
			body: $follow
		));

		$this->logger->expects($this->once())->method('debug')->with(
			'Unhandled Undo Follow request received',
			['inbox' => $this->inbox, 'message' => $message->toArray(), 'request' => $follow->toArray()]
		);
		$this->logger->expects($this->never())->method('error');

		$this->subject->handleRequest($request);
	}

	public function testItWillNotDelegateAnUndoMessageForARemoteMessageIfItHasNoClient() {
		$message = new Undo(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/outbox/' . $this->randomId(),
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');

		$this->logger->expects($this->once())->method('error')->with(
			'Unhandled Undo request received',
			['inbox' => null, 'message' => $message->toArray()] // Inbox is null since we are not using $this->subject.
		);

		(new class(verifier: $this->verifier, log: $this->logger) extends InboxAdapter {})->handleRequest($request);
	}

	public function testItWillNotDelegateAnUndoMessageForAnIncludedUnknownMessage() {
		$message = new Undo(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: new Activity(
				id: 'https://smol.blog/outbox/' . $this->randomId(),
				actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
				object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			),
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->never())->method('sendRequest');

		$this->logger->expects($this->once())->method('error')->with(
			'Unhandled Undo request received',
			['inbox' => $this->inbox, 'message' => $message->toArray()]
		);

		$this->subject->handleRequest($request);
	}

	public function testItWillDelegateADeleteMessageForAnIncludedActor() {
		$actor = new Actor(
			id: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			type: ActorType::Person,
		);
		$message = new Delete(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: $actor,
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->never())->method('sendRequest');

		$this->logger->expects($this->once())->method('debug')->with(
			'Unhandled Delete Actor request received',
			['inbox' => $this->inbox, 'message' => $message->toArray(), 'actor' => $actor->toArray()]
		);
		$this->logger->expects($this->never())->method('error');

		$this->subject->handleRequest($request);
	}

	public function testItWillDelegateADeleteMessageForARemoteActor() {
		$actor = new Actor(
			id: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			type: ActorType::Person,
		);
		$message = new Delete(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: $actor->id,
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->once())->method('sendRequest')->with(
			$this->httpMessageEqualTo(new HttpRequest(
				verb: HttpVerb::GET,
				url: $actor->id,
				headers: ['Accept' => 'application/json'],
			))
		)->willReturn(new HttpResponse(
			body: $actor
		));

		$this->logger->expects($this->once())->method('debug')->with(
			'Unhandled Delete Actor request received',
			['inbox' => $this->inbox, 'message' => $message->toArray(), 'actor' => $actor->toArray()]
		);
		$this->logger->expects($this->never())->method('error');

		$this->subject->handleRequest($request);
	}

	public function testItWillNotDelegateADeleteMessageForARemoteMessageIfItHasNoClient() {
		$message = new Delete(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: 'https://smol.blog/outbox/' . $this->randomId(),
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');

		$this->logger->expects($this->once())->method('error')->with(
			'Unhandled Delete request received',
			['inbox' => null, 'message' => $message->toArray()] // Inbox is null since we are not using $this->subject.
		);

		(new class(verifier: $this->verifier, log: $this->logger) extends InboxAdapter {})->handleRequest($request);
	}

	public function testItWillNotDelegateADeleteMessageForAnIncludedUnknownMessage() {
		$message = new Delete(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: new Activity(
				id: 'https://smol.blog/outbox/' . $this->randomId(),
				actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
				object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			),
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->never())->method('sendRequest');

		$this->logger->expects($this->once())->method('error')->with(
			'Unhandled Delete request received',
			['inbox' => $this->inbox, 'message' => $message->toArray()]
		);

		$this->subject->handleRequest($request);
	}

	public function testItWillLogAnErrorForAnUnknownMessage() {
		$message = new Activity(
			id: 'https://smol.blog/outbox/' . $this->randomId(),
			actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: new Activity(
				id: 'https://smol.blog/outbox/' . $this->randomId(),
				actor: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
				object: 'https://smol.blog/' . $this->randomId() . '/activitypub/actor',
			),
		);
		$request = new ServerRequest('POST', 'https://smol.blog/inbox', [], json_encode($message));

		$this->verifier->expects($this->never())->method('verify');
		$this->httpClient->expects($this->never())->method('sendRequest');

		$this->logger->expects($this->once())->method('error')->with(
			'Unknown ActivityPub message received',
			['inbox' => $this->inbox, 'body' => $message->toArray()]
		);
		$this->logger->expects($this->never())->method('debug');

		$this->subject->handleRequest($request);
	}
}
