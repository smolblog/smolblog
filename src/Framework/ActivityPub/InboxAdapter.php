<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\Objects\{ActivityPubBase, Actor, Delete, Follow, Undo};
use Smolblog\Framework\ActivityPub\Signatures\MessageVerifier;
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
	 * @param ObjectGetter|null    $getter   Optional ObjectGetter for getting remote objects.
	 * @param MessageVerifier|null $verifier Optional service to verify signed HTTP messages.
	 * @param LoggerInterface|null $log      Optional PSR logger to use.
	 */
	public function __construct(
		protected ?ObjectGetter $getter = null,
		protected ?MessageVerifier $verifier = null,
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
		$inboxContext = $this->determineInbox($request);

		if (
			$request->hasHeader('signature') &&
			isset($this->verifier) &&
			isset($this->getter) &&
			!$this->verifyRequest($request, $inboxContext)
		) {
			$this->log->info('ActivityPub message rejected for invalid signature.', [
					'method' => $request->getMethod(),
					'target' => $request->getRequestTarget(),
					'headers' => $request->getHeaders(),
					'body' => $request->getBody()->__toString(),
				]);
			return;
		}

		$bodyArray = json_decode($request->getBody()->__toString(), associative: true);
		$body = ActivityPubBase::typedObjectFromArray($bodyArray);

		switch (get_class($body)) {
			case Follow::class:
				$this->handleFollow(request: $body, inboxContext: $inboxContext);
				break;

			case Undo::class:
				$this->handleUndo(message: $body, inboxContext: $inboxContext);
				break;

			case Delete::class:
				$this->handleDelete(message: $body, inboxContext: $inboxContext);
				break;

			default:
				$this->handleUnknownMessage(body: $bodyArray, inboxContext: $inboxContext);
				break;
		}
	}

	/**
	 * Verify the signature on a request.
	 *
	 * @param ServerRequestInterface $request      Incoming request.
	 * @param InboxRequestContext    $inboxContext Context of the request.
	 * @return boolean
	 */
	private function verifyRequest(ServerRequestInterface $request, InboxRequestContext $inboxContext): bool {
		$sigMatches = [];
		preg_match('/keyId="([^"]*)"/', $request->getHeaderLine('signature'), $sigMatches);
		$sigUrl = $sigMatches[1] ?? '';
		$idParts = parse_url($sigUrl);

		if (!$idParts || !(isset($idParts['scheme']) && isset($idParts['host']))) {
			return false;
		}

		$response = $this->getter?->get(
			url: $sigUrl,
			signedWithPrivateKey: $inboxContext->privateKeyPem,
			withKeyId: $inboxContext->inboxActor?->publicKey->id,
		);
		if (!isset($response->publicKey)) {
			return false;
		}

		$results = $this->verifier->verify(
			request: $request,
			keyPem: $response->publicKey->publicKeyPem,
		);

		return $results;
	}

	/**
	 * Override this method to tag a request with the appropriate inbox context.
	 *
	 * @param ServerRequestInterface $request Incoming web request.
	 * @return InboxRequestContext Object identifying the inbox being hit.
	 */
	protected function determineInbox(ServerRequestInterface $request): ?InboxRequestContext {
		return new InboxRequestContext(inboxKey: null);
	}

	/**
	 * Handle an Undo message. Delegates to sub-methods by default and logs an `error` if unknown type.
	 *
	 * @param Undo                $message      Undo message received by the server.
	 * @param InboxRequestContext $inboxContext Inbox request was sent to.
	 * @return void
	 */
	protected function handleUndo(Undo $message, InboxRequestContext $inboxContext): void {
		$object = is_string($message->object) ? $this->getter?->get(
			url: $message->object,
			signedWithPrivateKey: $inboxContext->privateKeyPem,
			withKeyId: $inboxContext->inboxActor?->publicKey->id,
		) : $message->object;
		$object ??= new stdClass();

		switch (get_class($object)) {
			case Follow::class:
				$this->undoFollow(message: $message, request: $object, inboxContext: $inboxContext);
				break;

			default:
				$this->log->error('Unhandled Undo request received', [
					'inbox' => $inboxContext->inboxKey,
					'message' => $message->toArray(),
				]);
				break;
		}
	}

	/**
	 * Handle a Delete message. Delegates to sub-methods by default and logs an `error` if unknown type.
	 *
	 * @param Delete              $message      Delete message received by the server.
	 * @param InboxRequestContext $inboxContext Inbox message was sent to.
	 * @return void
	 */
	protected function handleDelete(Delete $message, InboxRequestContext $inboxContext): void {
		$object = is_string($message->object) ? $this->getter?->get(
			url: $message->object,
			signedWithPrivateKey: $inboxContext->privateKeyPem,
			withKeyId: $inboxContext->inboxActor?->publicKey->id,
		) : $message->object;
		$object ??= new stdClass();

		switch (get_class($object)) {
			case Actor::class:
				$this->deleteActor(message: $message, actor: $object, inboxContext: $inboxContext);
				break;

			default:
				$this->log->error('Unhandled Delete request received', [
					'inbox' => $inboxContext->inboxKey,
					'message' => $message->toArray(),
				]);
				break;
		}
	}

	/**
	 * Handle a Follow request.
	 *
	 * @param Follow              $request      Request received by the server.
	 * @param InboxRequestContext $inboxContext Inbox request was sent to.
	 * @return void
	 */
	protected function handleFollow(Follow $request, InboxRequestContext $inboxContext): void {
		$this->log->debug('Unhandled Follow request received', [
			'inbox' => $inboxContext->inboxKey,
			'message' => $request->toArray(),
		]);
	}

	/**
	 * Handle an Undo message containing a Follow request.
	 *
	 * @param Undo                $message      Undo message received by the server.
	 * @param Follow              $request      Request included in the message.
	 * @param InboxRequestContext $inboxContext Inbox request was sent to.
	 * @return void
	 */
	protected function undoFollow(Undo $message, Follow $request, InboxRequestContext $inboxContext): void {
		$this->log->debug('Unhandled Undo Follow request received', [
			'inbox' => $inboxContext->inboxKey,
			'message' => $message->toArray(),
			'request' => $request->toArray(),
		]);
	}

	/**
	 * Handle a Delete message containing an Actor.
	 *
	 * @param Delete              $message      Delete message received by the server.
	 * @param Actor               $actor        Actor included in the message.
	 * @param InboxRequestContext $inboxContext Inbox message was sent to.
	 * @return void
	 */
	protected function deleteActor(Delete $message, Actor $actor, InboxRequestContext $inboxContext): void {
		$this->log->debug('Unhandled Delete Actor request received', [
			'inbox' => $inboxContext->inboxKey,
			'message' => $message->toArray(),
			'actor' => $actor->toArray(),
		]);
	}

	/**
	 * Handle a message that did not parse to a known object. Logs an `error` by default.
	 *
	 * @param array               $body         Array-parsed body of the HTTP request.
	 * @param InboxRequestContext $inboxContext Inbox message was sent to.
	 * @return void
	 */
	protected function handleUnknownMessage(array $body, InboxRequestContext $inboxContext): void {
		$this->log->error('Unknown ActivityPub message received', [
			'inbox' => $inboxContext->inboxKey,
			'body' => $body,
		]);
	}
}
