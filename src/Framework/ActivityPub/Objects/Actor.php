<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;

/**
 * An ActivityPub Actor.
 */
readonly class Actor extends ActivityPubObject {
	/**
	 * Public key for the Actor.
	 *
	 * @var ActorPublicKey
	 */
	public ?ActorPublicKey $publicKey;

	/**
	 * Construct the object.
	 *
	 * @param string                        $id           Globally unique ID for the object. Usually a URL.
	 * @param ActorType                     $type         Type of this Actor.
	 * @param string|null                   $publicKeyPem PEM-formatted public key for this actor.
	 * @param string|array|JsonSerializable ...$props     Any additional properties.
	 */
	public function __construct(
		string $id,
		public ActorType $type,
		?string $publicKeyPem = null,
		string|array|JsonSerializable ...$props,
	) {
		$this->publicKey = isset($publicKeyPem) ? new ActorPublicKey(
			id: "$id#publicKey",
			owner: $id,
			publicKeyPem: $publicKeyPem,
		) : null;
		parent::__construct(...$props, id: $id);
	}

	/**
	 * Type of this Object.
	 *
	 * @return string
	 */
	public function type(): string {
		return $this->type->name;
	}

	/**
	 * Context for this object.
	 *
	 * @return string|array
	 */
	public function context(): string|array {
		$base = parent::context();

		if (!isset($this->publicKey)) {
			return $base;
		}

		if (!is_array($base)) {
			$base = [$base];
		}

		return [
			...$base,
			'https://w3id.org/security/v1'
		];
	}
}
