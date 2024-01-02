<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\Objects\{ActivityPubBase, Actor, Delete, Follow, Undo};
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;
use stdClass;

/**
 * Framework for implementing an ActivityPub inbox in a PHP app.
 *
 * Subclass InboxAdapter with your own class to implement ActivityPub in your app. Override the `handle` methods
 * to introduce functionality. There are also optional specific methods for Undo and Delete with different objects.
 * Anything not implemented is ignored.
 */
abstract class InboxAdapter {
	/**
	 * PSR standard logger to use.
	 *
	 * @var LoggerInterface
	 */
	protected LoggerInterface $log;

	/**
	 * Construct the adapter.
	 *
	 * @param ClientInterface|null $fetcher  Optional PSR HTTP client to use to get objects from URLs.
	 * @param MessageVerifier|null $verifier Optional service to verify signed HTTP messages.
	 * @param LoggerInterface|null $log      Optional PSR logger to use.
	 */
	public function __construct(
		private ?ClientInterface $fetcher = null,
		private ?MessageVerifier $verifier = null,
		?LoggerInterface $log = null
	) {
		$this->log = $log ?? new NullLogger();
	}

	/**
	 * Handle an incoming message to the inbox.
	 *
	 * @param ServerRequestInterface $request Incoming web request.
	 * @return void
	 */
	public function handleRequest(ServerRequestInterface $request): void {
		if (
			$request->hasHeader('signature') &&
			isset($this->verifier) &&
			isset($this->fetcher) &&
			!$this->verifyRequest($request)
		) {
			$this->log->info('Request provided invalid signature', ['request' => $request]);
			return;
		}

		$inboxKey = $this->determineInbox($request);
		$bodyArray = json_decode($request->getBody()->__toString(), associative: true);
		$body = ActivityPubBase::typedObjectFromArray($bodyArray);

		switch (get_class($body)) {
			case Follow::class:
				$this->handleFollow(request: $body, inboxKey: $inboxKey);
				break;

			case Undo::class:
				$this->handleUndo(message: $body, inboxKey: $inboxKey);
				break;

			case Delete::class:
				$this->handleDelete(message: $body, inboxKey: $inboxKey);
				break;

			default:
				$this->handleUnknownMessage(body: $bodyArray, inboxKey: $inboxKey);
				break;
		}
	}

	/**
	 * Verify the signature on a request.
	 *
	 * @param ServerRequestInterface $request Incoming request.
	 * @return boolean
	 */
	private function verifyRequest(ServerRequestInterface $request): bool {
		$sigMatches = [];
		preg_match('/keyId="([^"]*)"/', $request->getHeaderLine('signature'), $sigMatches);
		$sigUrl = $sigMatches[1] ?? '';
		$idParts = parse_url($sigUrl);

		if (!$idParts || !(isset($idParts['scheme']) && isset($idParts['host']))) {
			return false;
		}

		$response = $this->getRemoteObject($sigUrl);
		if (!isset($response->publicKey)) {
			return false;
		}

		return $this->verifier->verify(
			request: $request,
			keyId: $sigUrl,
			keyPem: $response->publicKey->publicKeyPem,
		);
	}

	/**
	 * Turn a URL into an ActivityPub object.
	 *
	 * @param string $url URL to fetch.
	 * @return ActivityPubBase|null
	 */
	protected function getRemoteObject(string $url): ?ActivityPubBase {
		if (!isset($this->fetcher)) {
			return null;
		}

		$response = $this->fetcher->sendRequest(new HttpRequest(
			verb: HttpVerb::GET,
			url: $url,
			headers: ['accept' => 'application/json'],
		));

		return ActivityPubBase::typedObjectFromArray(
			json_decode($response->getBody()->getContents(), associative: true)
		);
	}

	/**
	 * Override this method to tag a request with the inbox it was sent to.
	 *
	 * @param ServerRequestInterface $request Incoming web request.
	 * @return mixed Object identifying the inbox being hit.
	 */
	protected function determineInbox(ServerRequestInterface $request): mixed {
		return null;
	}

	/**
	 * Handle an Undo message. Delegates to sub-methods by default and logs an `error` if unknown type.
	 *
	 * @param Undo  $message  Undo message received by the server.
	 * @param mixed $inboxKey Inbox request was sent to.
	 * @return void
	 */
	protected function handleUndo(Undo $message, mixed $inboxKey): void {
		$object = is_string($message->object) ? $this->getRemoteObject($message->object) : $message->object;
		$object ??= new stdClass();

		switch (get_class($object)) {
			case Follow::class:
				$this->undoFollow(message: $message, request: $object, inboxKey: $inboxKey);
				break;

			default:
				$this->log->error('Unhandled Undo request received', [
					'inbox' => $inboxKey,
					'message' => $message->toArray(),
				]);
				break;
		}
	}

	/**
	 * Handle a Delete message. Delegates to sub-methods by default and logs an `error` if unknown type.
	 *
	 * @param Delete $message  Delete message received by the server.
	 * @param mixed  $inboxKey Inbox message was sent to.
	 * @return void
	 */
	protected function handleDelete(Delete $message, mixed $inboxKey): void {
		$object = is_string($message->object) ? $this->getRemoteObject($message->object) : $message->object;
		$object ??= new stdClass();

		switch (get_class($object)) {
			case Actor::class:
				$this->deleteActor(message: $message, actor: $object, inboxKey: $inboxKey);
				break;

			default:
				$this->log->error('Unhandled Delete request received', [
					'inbox' => $inboxKey,
					'message' => $message->toArray(),
				]);
				break;
		}
	}

	/**
	 * Handle a Follow request.
	 *
	 * @param Follow $request  Request received by the server.
	 * @param mixed  $inboxKey Inbox request was sent to.
	 * @return void
	 */
	protected function handleFollow(Follow $request, mixed $inboxKey): void {
		$this->log->debug('Unhandled Follow request received', [
			'inbox' => $inboxKey,
			'message' => $request->toArray(),
		]);
	}

	/**
	 * Handle an Undo message containing a Follow request.
	 *
	 * @param Undo   $message  Undo message received by the server.
	 * @param Follow $request  Request included in the message.
	 * @param mixed  $inboxKey Inbox request was sent to.
	 * @return void
	 */
	protected function undoFollow(Undo $message, Follow $request, mixed $inboxKey): void {
		$this->log->debug('Unhandled Undo Follow request received', [
			'inbox' => $inboxKey,
			'message' => $message->toArray(),
			'request' => $request->toArray(),
		]);
	}

	/**
	 * Handle a Delete message containing an Actor.
	 *
	 * @param Delete $message  Delete message received by the server.
	 * @param Actor  $actor    Actor included in the message.
	 * @param mixed  $inboxKey Inbox message was sent to.
	 * @return void
	 */
	protected function deleteActor(Delete $message, Actor $actor, mixed $inboxKey): void {
		$this->log->debug('Unhandled Delete Actor request received', [
			'inbox' => $inboxKey,
			'message' => $message->toArray(),
			'actor' => $actor->toArray(),
		]);
	}

	/**
	 * Handle a message that did not parse to a known object. Logs an `error` by default.
	 *
	 * @param array $body     Array-parsed body of the HTTP request.
	 * @param mixed $inboxKey Inbox message was sent to.
	 * @return void
	 */
	protected function handleUnknownMessage(array $body, mixed $inboxKey): void {
		$this->log->error('Unknown ActivityPub message received', [
			'inbox' => $inboxKey,
			'body' => $body,
		]);
	}
}
