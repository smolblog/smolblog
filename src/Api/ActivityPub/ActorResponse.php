<?php

namespace Smolblog\Api\ActivityPub;

/**
 * ActivityPub response for an Actor.
 */
class ActorResponse extends ActivityPubObject {
	/**
	 * Create the response.
	 *
	 * @param string      $id                ID (usually the URI) for this document.
	 * @param ActorType   $type              Type of this actor.
	 * @param string      $inbox             URL to private inbox for this Actor.
	 * @param string      $outbox            URL to outbox for this Actor.
	 * @param string|null $preferredUsername Handle for this Actor on this server.
	 * @param string|null $name              Display name for this Actor.
	 * @param string|null $summary           Description/summary for this Actor.
	 * @param string|null $following         URL to accounts this Actor is following.
	 * @param string|null $followers         URL to accounts following this Actor.
	 * @param string|null $sharedInbox       URL to the public shared inbox on this server.
	 * @param string|null $publicKeyPem      Public key for this Actor.
	 */
	public function __construct(
		string $id,
		ActorType $type,
		public readonly string $inbox,
		public readonly string $outbox,
		public readonly ?string $preferredUsername = null,
		public readonly ?string $name = null,
		public readonly ?string $summary = null,
		public readonly ?string $following = null,
		public readonly ?string $followers = null,
		public readonly ?string $sharedInbox = null,
		public readonly ?string $publicKeyPem = null,
	) {
		parent::__construct(id: $id, type: $type->value);
	}

	/**
	 * Serialize this response and add any extra JSON information.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$base = parent::toArray();

		$base['@context'] = [
			'https://www.w3.org/ns/activitystreams',
			'https://w3id.org/security/v1'
		];

		unset($base['publicKeyPem']);
		if (isset($this->publicKeyPem)) {
			$base['publicKey'] = [
				'id' => $this->id . '#publicKey',
				'owner' => $this->id,
				'publicKeyPem' => $this->publicKeyPem
			];
		}

		return $base;
	}
}
