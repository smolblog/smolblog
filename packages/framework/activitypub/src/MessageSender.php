<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubObject;
use Smolblog\Framework\ActivityPub\Signatures\MessageSigner;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;

/**
 * Service for sending messages to inboxes.
 */
class MessageSender {
	/**
	 * PSR standard logger to use.
	 *
	 * @var LoggerInterface
	 */
	protected LoggerInterface $log;

	/**
	 * Construct the service.
	 *
	 * Set throwOnError: true to have the service throw an ActivityPubException when the remote server gives an
	 * error response; default (false) is to log the error.
	 *
	 * @param ClientInterface      $fetcher      PSR HTTP client.
	 * @param MessageSigner|null   $signer       Optional PSR logger.
	 * @param LoggerInterface|null $log          Optional PSR logger to use.
	 * @param boolean              $throwOnError True to throw exceptions when the remote server gives an error.
	 */
	public function __construct(
		private ClientInterface $fetcher,
		private ?MessageSigner $signer = null,
		?LoggerInterface $log = null,
		private bool $throwOnError = false,
	) {
		$this->log = $log ?? new NullLogger();
	}

	/**
	 * Send an ActivityPub message to an inbox.
	 *
	 * Optionally provide a private key and key ID to sign the request with. Both a key and ID must be provided to sign.
	 *
	 * @throws ActivityPubException When invalid parameters are given or the external service gives an error.
	 *
	 * @param ActivityPubObject $message              Message to send.
	 * @param string            $toInbox              URL of the inbox to send it to.
	 * @param string|null       $signedWithPrivateKey PEM-formatted private key.
	 * @param string|null       $withKeyId            ID of the key being used.
	 * @return void
	 */
	public function send(
		ActivityPubObject $message,
		string $toInbox,
		?string $signedWithPrivateKey = null,
		?string $withKeyId = null,
	): void {
		if (($signedWithPrivateKey || $withKeyId) && !($signedWithPrivateKey && $withKeyId)) {
			throw new ActivityPubException('A private key and key ID must both be provided if either is given.');
		}

		$request = new HttpRequest(
			verb: HttpVerb::POST,
			url: $toInbox,
			body: $message,
		);

		if ($signedWithPrivateKey && $this->signer) {
			$request = $this->signer->sign(
				request: $request,
				keyId: $withKeyId,
				keyPem: $signedWithPrivateKey,
			);
		}

		$this->log->debug("Sending {$message->type()} to $toInbox");

		$acceptResponse = $this->fetcher->sendRequest($request);
		$resCode = $acceptResponse->getStatusCode();
		if ($resCode >= 300 || $resCode < 200) {
			$errorMessage = 'Error from federated server: ' . $acceptResponse->getBody()->getContents();

			if ($this->throwOnError) {
				throw new ActivityPubException($errorMessage);
			}

			$this->log->error($errorMessage, [
				'message' => $message->toArray(),
				'inbox' => $toInbox,
				'signed' => $request->hasHeader('signature') ? "With key $withKeyId" : 'NO',
			]);
		}
	}
}
