<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;

/**
 * An Echo request object.
 *
 * @see https://verify.funfedi.dev/
 */
readonly class EchoRequest extends ActivityPubObject implements JsonSerializable {
	/**
	 * Construct the object.
	 *
	 * @param string                             $id       Optional ID of the object.
	 * @param null|string|array|JsonSerializable ...$props Any additional properties.
	 */
	public function __construct(
		string $id = '',
		null|string|array|JsonSerializable ...$props
	) {
		parent::__construct($id, ...$props);
	}
}
