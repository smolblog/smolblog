<?php

namespace Smolblog\Api\ActivityPub;

use Smolblog\Framework\Objects\Value;

class ActorResponse extends ActivityPubObject {
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
	)
	{
		parent::__construct(id: $id, type: $type->value);
	}

	public function toArray(): array
	{
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
