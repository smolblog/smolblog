<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\Objects\{ActivityPubBase, Actor, Delete, Follow, Undo};

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
		$inboxKey = $this->determineInbox($request);

		$body = $this->parseBody($request->getBody()->getContents());
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
	 * Parse the JSON body of the request and return the resulting object.
	 *
	 * @param string $body Body of the inbox request.
	 * @return ActivityPubBase|null
	 */
	protected function parseBody(string $body): ?ActivityPubBase {
		$bodyArray = json_decode($body, associative: true);
		switch (strtolower($bodyArray['type'])) {
			case 'follow':
				return Follow::fromArray($bodyArray);

			case 'undo':
				return Undo::fromArray($bodyArray);

			case 'delete':
				return Delete::fromArray($bodyArray);
		}

		return null;
	}

	/**
	 * Handle an Undo message.
	 *
	 * @param Undo  $message  Undo message received by the server.
	 * @param mixed $inboxKey Inbox request was sent to.
	 * @return void
	 */
	protected function handleUndo(Undo $message, mixed $inboxKey): void {
	}

	/**
	 * Handle a Delete message.
	 *
	 * @param Delete $message  Delete message received by the server.
	 * @param mixed  $inboxKey Inbox message was sent to.
	 * @return void
	 */
	protected function handleDelete(Delete $message, mixed $inboxKey): void {
	}

	/**
	 * Handle a Follow request.
	 *
	 * @param Follow $request  Request received by the server.
	 * @param mixed  $inboxKey Inbox request was sent to.
	 * @return void
	 */
	protected function handleFollow(Follow $request, mixed $inboxKey): void {
	}

	/**
	 * Handle an Undo message containing a Follow request. Delegates to $this->handleUndo by default.
	 *
	 * @param Undo   $message  Undo message received by the server.
	 * @param Follow $request  Request included in the message.
	 * @param mixed  $inboxKey Inbox request was sent to.
	 * @return void
	 */
	protected function undoFollow(Undo $message, Follow $request, mixed $inboxKey): void {
		$this->handleUndo(message: $message, inboxKey: $inboxKey);
	}

	/**
	 * Handle a Delete message containing an Actor. Delegates to $this->handleDelete by default.
	 *
	 * @param Delete $message  Delete message received by the server.
	 * @param Actor  $actor    Actor included in the message.
	 * @param mixed  $inboxKey Inbox message was sent to.
	 * @return void
	 */
	protected function deleteActor(Delete $message, Actor $actor, mixed $inboxKey): void {
		$this->handleDelete(message: $message, inboxKey: $inboxKey);
	}
}
