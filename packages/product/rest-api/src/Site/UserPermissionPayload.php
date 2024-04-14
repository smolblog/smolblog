<?php

namespace Smolblog\Api\Site;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value;

/**
 * Payload for setting user permissions on a site.
 */
readonly class UserPermissionPayload extends Value {
	/**
	 * Construct the payload.
	 *
	 * @param Identifier $userId   ID of the user.
	 * @param boolean    $isAdmin  True if user should be an administrator.
	 * @param boolean    $isAuthor True if user should be able to author content.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly bool $isAdmin = false,
		public readonly bool $isAuthor = false,
	) {
	}

	/**
	 * Deserialize properly.
	 *
	 * @param array $data Serialized array.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		return parent::deserializeValue([
			...$data,
			'userId' => Identifier::fromString($data['userId']),
		]);
	}
}
