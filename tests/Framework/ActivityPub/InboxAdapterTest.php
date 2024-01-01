<?php

namespace Smolblog\Framework\ActivityPub;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Framework\ActivityPub\Objects\Follow;
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
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: '//smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: '//smol.blog/' . $this->randomId() . '/activitypub/actor',
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

	public function testItWillHandleAFollowMessage() {
		$message = new Follow(
			id: '//smol.blog/outbox/' . $this->randomId(),
			actor: '//smol.blog/' . $this->randomId() . '/activitypub/actor',
			object: '//smol.blog/' . $this->randomId() . '/activitypub/actor',
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
}
